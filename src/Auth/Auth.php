<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Auth;

use Exception;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use League\OAuth2\Client\Provider\AbstractProvider;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UriInterface as Uri;
use RuntimeException;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Session\SessionInterface;
use Zend\Expressive\Session\SessionMiddleware;

class Auth implements MiddlewareInterface
{
    private $providerFactory;
    private $unauthorizedResponseFactory;

    /**
     * Constructor
     *
     * @param array $config Configuration for the Opauth instance
     */
    public function __construct(
        OAuth2ProviderFactory $providerFactory,
        callable $unauthorizedResponseFactory
    ) {
        $this->providerFactory             = $providerFactory;
        $this->unauthorizedResponseFactory = $unauthorizedResponseFactory;
    }

    /**
     * @return Response
     */
    public function process(Request $request, DelegateInterface $delegate)
    {
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);

        $provider = $this->providerFactory->createProvider(
            $request->getAttribute('provider')
        );
        $params = $request->getQueryParams();
        $oauth2Session = $this->getOAuth2SessionData($session);

        if (! empty($params['error'])) {
            return $this->processError($params['error']);
        }

        if (empty($params['code'])) {
            return $this->requestAuthorization(
                $provider,
                $request->getUri(),
                $session,
                $oauth2Session,
                $params['redirect'] ?? ''
            );
        }

        if (empty($params['state'])
            || $params['state'] !== $oauth2Session['state']
        ) {
            return $this->displayUnauthorizedPage($request, $params['redirect'] ?? '');
        }


        try {
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $params['code'],
            ]);

            $user = $provider->getResourceOwner($token);
        } catch (Exception $e) {
            return $this->processError($e);
        }

        $oauth2Session['user'] = $user->toArray();
        $session->set('auth', $oauth2Session);

        return new RedirectResponse($oauth2Session['redirect'] ?? '/');
    }

    /**
     * @param string|\Throwable
     * @throws Exception
     */
    private function processError($error)
    {
        if (is_string($error)) {
            throw new RuntimeException($error, 401);
        }
        throw new RuntimeException($error->getMessage(), 401, $error);
    }

    private function requestAuthorization(
        AbstractProvider $provider,
        Uri $uri,
        SessionInterface $session,
        array $sessionData,
        string $redirect
    ) : Response {
        // Authorization URL MUST be generated BEFORE we retrieve the state,
        // as it is responsible for generating the state in the first place!
        $authorizationUrl = $provider->getAuthorizationUrl();

        if (! empty($redirect)) {
            $sessionData['redirect'] = $redirect;
        }

        $sessionData['state'] = $provider->getState();
        $session->set('auth', $sessionData);

        return new RedirectResponse($authorizationUrl);
    }

    private function displayUnauthorizedPage(Request $request, string $redirect) : Response
    {
        $uri     = $request->getUri();
        $factory = $this->unauthorizedResponseFactory;

        return $factory($request->withUri(
            $redirect ? $uri->withPath($redirect) : $uri
        ));
    }

    private function getOAuth2SessionData(SessionInterface $session)
    {
        $data = $session->get('auth');
        if (! is_array($data)) {
            $data = [];
        }
        return $data;
    }
}
