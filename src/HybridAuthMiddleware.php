<?php
namespace Mwop;

use Exception;
use Hybrid_Auth;

class HybridAuthMiddleware
{
    private $auth;

    /**
     * Constructor
     * 
     * @param array $config Configuration for the Hybrid_Auth instance
     */
    public function __construct(Hybrid_Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Uses HybridAuth configuratoin to authenticate a user
     *
     * If the query string is missing, or does not include the provider,
     * invokes an error.
     *
     * If the provider requested is not configured, invokes an error.
     *
     * If authentication fails, invokes a 401 error.
     *
     * On success, sets the request's hybridAuth property with the user
     * details.
     * 
     * @param \Psr\Http\Message\RequestInterface $req 
     * @param \Phly\Conduit\Http\ResponseInterface $res 
     * @param callable $next 
     */
    public function __invoke($req, $res, $next)
    {
        if (! $req->getMethod() === 'POST') {
            $res->setStatusCode(405);
            return $next('POST');
        }

        if (! $req->query || ! isset($req->query['provider'])) {
            $res->setStatusCode(400);
            return $next('Missing authentication provider in query string');
        }

        $provider = $req->query['provider'];
        if (! isset($this->config['providers'][$provider])) {
            $res->setStatusCode(400);
            return $next('Invalid authentication provider');
        }

        // Sessions are required for Hybrid_Auth
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        try {
            $user = $this->auth->authenticate($provider);
            $req->hybridAuth = $user;
        } catch (Exception $e) {
            $res->setStatusCode(401);
            return $next('Authentication failure');
        }

        $next();
    }
}
