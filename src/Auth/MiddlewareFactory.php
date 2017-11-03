<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Auth;

use Interop\Container\ContainerInterface;
use Zend\Expressive\AppFactory;
use Zend\Expressive\Session\SessionMiddleware;

class MiddlewareFactory
{
    const ROUTE_DEBUG = '/{provider:debug|github|google}[/oauth2callback]';
    const ROUTE_PROD  = '/{provider:github|google}[/oauth2callback]';

    public function __invoke(ContainerInterface $container)  : callable
    {
        $middleware = AppFactory::create($container);

        $config = $container->get('config');
        $debug  = $config['debug'] ?? false;
        $route  = $debug ? self::ROUTE_DEBUG : self::ROUTE_PROD;

        $middleware->pipe(SessionMiddleware::class);

        $middleware->get($route, Auth::class);
        $middleware->get('/logout', Logout::class);

        if ($debug) {
            $middleware->get('/debug/authorize', DebugAuthorization::class);
        }

        $middleware->pipeRoutingMiddleware();
        $middleware->pipeDispatchMiddleware();

        return $middleware;
    }
}
