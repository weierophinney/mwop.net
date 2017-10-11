<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Auth;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Expressive\Session\SessionMiddleware;

class UserSession implements MiddlewareInterface
{
    /**
     * @return Response
     */
    public function process(Request $request, DelegateInterface $delegate)
    {
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $auth = $session->get('auth') ?? [];
        return $delegate->process($request->withAttribute('user', $auth['user'] ?? ''));
    }
}
