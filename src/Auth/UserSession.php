<?php
namespace Mwop\Auth;

use Aura\Session\Session;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserSession
{
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        $auth = $this->session->getSegment('auth');
        return $next($request->withAttribute('user', $auth->get('user')), $response);
    }
}
