<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Auth;

use Interop\Container\ContainerInterface;
use Zend\Expressive\AppFactory;

class MiddlewareFactory
{
    public function __invoke(ContainerInterface $container)  : callable
    {
        $middleware = AppFactory::create($container);

        $middleware->get('/{provider:github|google}[/oauth2callback]', Auth::class);
        $middleware->get('/logout', Logout::class);

        $middleware->pipeRoutingMiddleware();
        $middleware->pipeDispatchMiddleware();

        return $middleware;
    }
}
