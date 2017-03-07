<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Auth;

use Aura\Session\Session;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Diactoros\Response\RedirectResponse;

class Logout implements MiddlewareInterface
{
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @return RedirectResponse
     */
    public function process(Request $request, DelegateInterface $delegate)
    {
        $auth = $this->session->getSegment('auth');
        $user = $auth->get('user');
        if (! $user) {
            return $this->redirect($request);
        }

        $auth->clear();
        return $this->redirect($request);
    }

    private function redirect(Request $request) : RedirectResponse
    {
        $originalUri = $request->getAttribute('originalRequest', $request)->getUri();
        $redirectUri = $originalUri->withPath('/');

        return new RedirectResponse((string) $redirectUri);
    }
}
