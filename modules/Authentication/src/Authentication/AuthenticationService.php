<?php

namespace Authentication;

use Zend\Authentication\AuthenticationService as ZfAuthService,
    Zend\Authentication\Adapter\Digest as DigestAdapter;

class AuthenticationService
{
    protected $filename;
    protected $realm;
    protected $authentication;

    public function __construct($filename, $realm)
    {
        $this->filename = $filename;
        $this->realm    = $realm;
    }

    public function setAuthentication(ZfAuthService $authentication)
    {
        $this->authentication = $authentication;
    }

    public function getAuthentication()
    {
        if (null === $this->authentication) {
            $this->setAuthentication(new ZfAuthService());
        }
        return $this->authentication;
    }

    public function login($username, $password)
    {
        $adapter = new DigestAdapter($this->filename, $this->realm, $username, $password);
        $auth    = $this->getAuthentication();
        $result  = $auth->authenticate($adapter);
        return $result;
    }

    public function logout()
    {
        $auth = $this->getAuthentication();
        if (!$auth->hasIdentity()) {
            return;
        }
        $auth->clearIdentity();
    }
}
