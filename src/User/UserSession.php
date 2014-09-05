<?php
namespace Mwop\User;

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
        $request->user = $auth->get('user');
        $next();
    }
}
