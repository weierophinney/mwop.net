<?php
namespace Mwop\Auth;

use Aura\Session\Session;
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

    public function __invoke($req, $res, $next)
    {
        if ($req->getMethod() !== 'GET') {
            $res->setStatusCode(405);
            return $next('GET');
        }

        if (isset($req->query['redirect'])) {
            $redirect = $this->session->getSegment('redirect');
            $redirect->set('auth', $req->query['redirect']);
        }

        $auth = new Opauth($this->config);
        $res->end();
    }
}
