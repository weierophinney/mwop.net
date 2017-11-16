<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Expressive\Session\SessionMiddleware;

class LogoutHandler implements MiddlewareInterface
{
    public function process(Request $request, DelegateInterface $delegate) : RedirectResponse
    {
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $auth = $session->get('auth') ?? [];
        $user = $auth['user'] ?? false;
        if (! $user) {
            return $this->redirect($request);
        }

        $session->clear();
        return $this->redirect($request);
    }

    private function redirect(Request $request) : RedirectResponse
    {
        $originalUri = $request->getAttribute('originalRequest', $request)->getUri();
        $redirectUri = $originalUri->withPath('/');

        return new RedirectResponse((string) $redirectUri);
    }
}
