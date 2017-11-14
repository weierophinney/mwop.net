<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Phly\OAuth2ClientAuthentication\Debug;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DebugProviderMiddleware implements MiddlewareInterface
{
    private $redirectResponseFactory;

    public function __construct(callable $redirectResponseFactory)
    {
        $this->redirectResponseFactory = $redirectResponseFactory;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate) : ResponseInterface
    {
        $uri = sprintf(
            '/auth/debug/oauth2callback?code=%s&state=%s',
            DebugProvider::CODE,
            DebugProvider::STATE
        );

        return ($this->redirectResponseFactory)($uri);
    }
}
