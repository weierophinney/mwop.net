<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Auth;

use Aura\Session\Segment;
use Aura\Session\Session;
use Exception;
use League\OAuth2\Client\Provider\AbstractProvider;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UriInterface as Uri;
use RuntimeException;
use Zend\Diactoros\Response\RedirectResponse;

class Auth
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

    public function __invoke(Request $req, Response $res, callable $next) : Response
    {
        $provider = $this->providerFactory->createProvider(
            $req->getAttribute('provider')
        );
        $params = $req->getQueryParams();
        $oauth2Session = $this->session->getSegment('auth');

        if (! empty($params['error'])) {
            return $this->processError($params['error']);
        }

        if (empty($params['code'])) {
            return $this->requestAuthorization(
                $provider,
                $req->getUri(),
                $oauth2Session,
                $params['redirect'] ?? ''
            );
        }

        if (empty($params['state'])
            || $params['state'] !== $oauth2Session->get('state')
        ) {
            $oauth2Session->set('state', null);
            return $this->displayUnauthorizedPage($request, $oauth2Session, $params['redirect'] ?? '');
        }


        try {
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $params['code'],
            ]);

            $user = $provider->getResourceOwner($token);
        } catch (Exception $e) {
            return $this->processError($params['error']);
        }

        $oauth2Session->set('user', $user->toArray());

        return new RedirectResponse($session->get('redirect') ?: '/');
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
        $session->set('state', $provider->getState());
        if (! empty($redirect)) {
            $session->set('redirect', $redirect);
        }
        return new RedirectResponse($provider->getAuthorizationUrl());
    }

    private function displayUnauthorizedPage(Segment $session, Request $request, string $redirect) : Response
    {
        $session->set('state', null);

        $uri     = $request->getUri();
        $factory = $this->unauthorizedResponseFactory;

        return $factory($request->withUri(
            $redirect ? $uri->withPath($redirect) : $uri
        ));
    }
}
