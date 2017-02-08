<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

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
        $user = $auth->get('user');
        error_log(sprintf('User discovered in session: %s', var_export($user, true)));
        return $next($request->withAttribute('user', $auth->get('user')), $response);
    }
}
