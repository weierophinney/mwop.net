<?php
namespace Mwop\Auth;

use Zend\Expressive\AppFactory;

class MiddlewareFactory
{
    public function __invoke($services)
    {
        $middleware = AppFactory::create($services);

        $middleware->route('/callback', AuthCallback::class, ['GET', 'POST']);
        $middleware->get('/github', Auth::class);
        $middleware->get('/google', Auth::class);
        $middleware->get('/twitter', Auth::class);
        $middleware->get('/logout', Logout::class);

        return $middleware;
    }
}
