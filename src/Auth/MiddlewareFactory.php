<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Auth;

use Interop\Container\ContainerInterface;
use Zend\Expressive\AppFactory;
use Zend\Expressive\Helper\BodyParams\BodyParamsMiddleware;

class MiddlewareFactory
{
    public function __invoke(ContainerInterface $container)  : callable
    {
        $middleware = AppFactory::create($container);

        $middleware->route('/callback', [
            BodyParamsMiddleware::class,
            AuthCallback::class,
        ], ['GET', 'POST']);
        $middleware->get('/github', Auth::class);
        $middleware->get('/google', Auth::class);
        $middleware->get('/twitter', Auth::class);

        $middleware->get('/github/oauth2callback', Auth::class);
        $middleware->get('/google/oauth2callback', Auth::class);
        $middleware->get('/twitter/oauth2callback', Auth::class);

        $middleware->get('/logout', Logout::class);

        $middleware->pipeRoutingMiddleware();
        $middleware->pipeDispatchMiddleware();

        return $middleware;
    }
}
