<?php
namespace Mwop\Auth;

use Zend\Expressive\AppFactory;
use Zend\Expressive\Helper\BodyParams\BodyParamsMiddleware;

class MiddlewareFactory
{
    public function __invoke($container)
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
