<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Auth;

use Aura\Session\Segment;
use Aura\Session\Session;
use Exception;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use League\OAuth2\Client\Provider\AbstractProvider;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UriInterface as Uri;
use RuntimeException;
use Zend\Diactoros\Response\RedirectResponse;

class Auth implements MiddlewareInterface
{
    private $providerFactory;
    private $session;
    private $unauthorizedResponseFactory;

    /**
     * Constructor
     *
     * @param array $config Configuration for the Opauth instance
     */
    public function __construct(
        OAuth2ProviderFactory $providerFactory,
        Session $session,
        callable $unauthorizedResponseFactory
    ) {
        $this->providerFactory             = $providerFactory;
        $this->session                     = $session;
        $this->unauthorizedResponseFactory = $unauthorizedResponseFactory;
    }

    /**
     * @return Response
     */
    public function process(Request $request, DelegateInterface $delegate)
    {
        $provider = $this->providerFactory->createProvider(
            $request->getAttribute('provider')
        );
        $params = $request->getQueryParams();
        $oauth2Session = $this->session->getSegment('auth');

        if (! empty($params['error'])) {
            return $this->processError($params['error']);
        }

        if (empty($params['code'])) {
            return $this->requestAuthorization(
                $provider,
                $request->getUri(),
                $oauth2Session,
                $params['redirect'] ?? ''
            );
        }

        if (empty($params['state'])
            || $params['state'] !== $oauth2Session->get('state')
        ) {
            return $this->displayUnauthorizedPage($oauth2Session, $request, $params['redirect'] ?? '');
        }


        try {
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $params['code'],
            ]);

            $user = $provider->getResourceOwner($token);
        } catch (Exception $e) {
            return $this->processError($e);
        }

        $oauth2Session->set('user', $user->toArray());
        $this->session->commit();

        return new RedirectResponse($oauth2Session->get('redirect') ?: '/');
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
        Segment $session,
        string $redirect
    ) : Response {
        // Authorization URL MUST be generated BEFORE we retrieve the state,
        // as it is responsible for generating the state in the first place!
        $authorizationUrl = $provider->getAuthorizationUrl();

        if (! empty($redirect)) {
            $session->set('redirect', $redirect);
        }

        $session->set('state', $provider->getState());
        $this->session->commit();

        return new RedirectResponse($authorizationUrl);
    }

    private function displayUnauthorizedPage(Segment $session, Request $request, string $redirect) : Response
    {
        $uri     = $request->getUri();
        $factory = $this->unauthorizedResponseFactory;

        return $factory($request->withUri(
            $redirect ? $uri->withPath($redirect) : $uri
        ));
    }
}
