<?php
namespace Mwop\User;

use Aura\Session\Session;
use Opauth;

class AuthCallback
{
    private $config;
    private $session;

    public function __construct(array $authConfig, Session $session)
    {
        $this->config  = $authConfig;
        $this->session = $session;
    }

    public function __invoke($req, $res, $next)
    {
        $auth         = new Opauth($this->config, false);
        $authResponse = null;

        $this->session->start();
        switch($auth->env['callback_transport']) {
            case 'session':
                $authResponse = $_SESSION['opauth'];
                unset($_SESSION['opauth']);
                break;
            case 'post':
                $authResponse = unserialize(base64_decode($req->body['opauth']));
                break;
            case 'get':
                $authResponse = unserialize(base64_decode($req->query['opauth']));
                break;
            default:
                $res->setStatusCode(400);
                return $next('Invalid request');
                break;
        }

        if (array_key_exists('error', $authResponse)) {
            $res->setStatusCode(403);
            return $next('Error authenticating');
        }

        if (empty($authResponse['auth'])
            || empty($authResponse['timestamp'])
            || empty($authResponse['signature'])
            || empty($authResponse['auth']['provider'])
            || empty($authResponse['auth']['uid'])
        ) {
            $res->setStatusCode(403);
            return $next('Invalid authentication response');
        } 
        
        if ($auth->env['callback_transport'] !== 'session'
            && ! $auth->validate(
                sha1(print_r($authResponse['auth'], true)),
                $authResponse['timestamp'],
                $response['signature'],
                $reason
            )
        ) {
            $res->setStatusCode(403);
            return $next('Invalid authentication response');
        }

        $auth = $this->session->getSegment('auth');
        $auth->set('user', $authResponse['auth']);

        $url      = (string) $req->getUrl()->setPath('/');
        $redirect = $this->session->getSegment('redirect')->get('auth');
        if ($redirect) {
            $url = $redirect;
            $this->session->getSegment('redirect')->set('auth', null);
        }

        $res->setStatusCode(302);
        $res->addHeader('Location', $url);
        $res->end();
    }
}
