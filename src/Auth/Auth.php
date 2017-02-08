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
            error_log('Parameters include an error: ' . $params['error']);
            return $this->processError($params['error']);
        }

        if (empty($params['code'])) {
            error_log('No code in query params; requesting authorization');
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
            error_log(sprintf(
                'Code provided, but query state ("%s") does not match session state ("%s")',
                $params['state'],
                $oauth2Session->get('state')
            ));
            return $this->displayUnauthorizedPage($oauth2Session, $req, $params['redirect'] ?? '');
        }


        try {
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $params['code'],
            ]);

            $user = $provider->getResourceOwner($token);
        } catch (Exception $e) {
            error_log(sprintf(
                'Exception occurred fetching access token and/or resource owner: %s',
                $e->getMessage()
            ));
            return $this->processError($e);
        }

        $oauth2Session->set('user', $user->toArray());

        error_log(sprintf(
            'Setting user in session: %s',
            var_export($user->toArray(), true)
        ));

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
        $session->set('state', $provider->getState());
        if (! empty($redirect)) {
            $session->set('redirect', $redirect);
        }

        $this->session->commit();

        return new RedirectResponse($provider->getAuthorizationUrl());
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
