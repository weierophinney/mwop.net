<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Auth;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\RedirectResponse;

class DebugAuthorization
{
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) : RedirectResponse {
        return new RedirectResponse(sprintf(
            '/auth/debug/oauth2callback?code=%s&state=%s',
            DebugProvider::CODE,
            DebugProvider::STATE
        ));
    }
}
