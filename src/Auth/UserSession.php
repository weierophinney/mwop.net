<?php
namespace Mwop\Auth;

use Aura\Session\Session;

class UserSession
{
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function __invoke($request, $response, $next)
    {
        $auth = $this->session->getSegment('auth');
        return $next($request->withAttribute('user', $auth->get('user')));
    }
}
