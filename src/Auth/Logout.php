<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Auth;

use Aura\Session\Session;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Logout
{
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        $auth = $this->session->getSegment('auth');
        $user = $auth->get('user');
        if (! $user) {
            return $this->redirect($request, $response);
        }

        $auth->clear();
        return $this->redirect($request, $response);
    }

    private function redirect(Request $request, Response $response) : Response
    {
        $originalUri = $request->getAttribute('originalRequest', $request)->getUri();
        $redirectUri = $originalUri->withPath('/');

        return $response
            ->withStatus(302)
            ->withHeader('Location', (string) $redirectUri);
    }
}
