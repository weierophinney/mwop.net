<?php
namespace Mwop\Auth;

use Aura\Session\Session;
use Phly\Http\Uri;

class Logout
{
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function __invoke($request, $response, $next)
    {
        $auth = $this->session->getSegment('auth');
        $user = $auth->get('user');
        if (! $user) {
            return $this->redirect($request, $response);
        }

        $auth->clear();
        return $this->redirect($request, $response);
    }

    private function redirect($request, $response)
    {
        $originalUrl = new Uri($request->originalUrl);
        $redirectUrl = (string) $originalUrl->setPath('/');
        $response->setStatus(302);
        $response->addHeader('Location', $redirectUrl);
        $response->end();
    }
}
