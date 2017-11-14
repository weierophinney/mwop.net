<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Phly\OAuth2ClientAuthentication;

use Interop\Container\ContainerInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Zend\Expressive\AppFactory;
use Zend\Expressive\Authentication\AuthenticationMiddleware;
use Zend\Expressive\Session\SessionMiddleware;
use Zend\Stratigility\MiddlewarePipe;

class OAuth2CallbackMiddlewareFactory
{
    const ROUTE_DEBUG = '/{provider:debug|github|google}[/oauth2callback]';
    const ROUTE_PROD  = '/{provider:github|google}[/oauth2callback]';

    public function __invoke(ContainerInterface $container) : MiddlewareInterface
    {
        /** @var MiddlewarePipe $pipeline */
        $pipeline = AppFactory::create($container);

        $config = $container->get('config');
        $debug  = $config['debug'] ?? false;
        $route  = $debug ? self::ROUTE_DEBUG : self::ROUTE_PROD;

        // OAuth2 providers rely on session to persist the user details
        $pipeline->pipe(SessionMiddleware::class);
        $pipeline->get($route, AuthenticationMiddleware::class);

        if ($debug) {
            $pipeline->get('/debug/authorize', Debug\DebugProviderMiddleware::class);
        }

        $pipeline->pipeRoutingMiddleware();
        $pipeline->pipeDispatchMiddleware();

        return $pipeline;
    }
}
