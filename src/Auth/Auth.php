<?php
namespace Mwop\Auth;

use Aura\Session\Session;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Opauth;

class Auth
{
    private $config;
    private $session;

    /**
     * Constructor
     *
     * @param array $config Configuration for the Opauth instance
     */
    public function __construct(array $authConfig, Session $session)
    {
        $this->config   = $authConfig;
        $this->session  = $session;
    }

    public function __invoke(Request $req, Response $res, callable $next) : Response
    {
        if (isset($req->getQueryParams()['redirect'])) {
            $redirect = $this->session->getSegment('redirect');
            $redirect->set('auth', $req->getQueryParams()['redirect']);
        }

        $auth = new Opauth($this->config);
        return $res;
    }
}
