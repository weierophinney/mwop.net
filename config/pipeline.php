<?php

declare(strict_types=1);

namespace Mwop;

use Middlewares\Csp;
use Phly\Expressive\OAuth2ClientAuthentication\OAuth2CallbackMiddleware;
use Zend\Expressive\Helper;
use Zend\Expressive\Handler\NotFoundHandler;
use Zend\Expressive\Session\SessionMiddleware;
use Zend\Stratigility\Middleware\ErrorHandler;
use Zend\Stratigility\Middleware\OriginalMessages;

return function (
    \Zend\Expressive\Application $app,
    \Zend\Expressive\MiddlewareFactory $factory,
    \Psr\Container\ContainerInterface $container
) : void {
    $app->pipe(OriginalMessages::class);
    $app->pipe(XClacksOverhead::class);
    $app->pipe(XPoweredBy::class);
    $app->pipe(Csp::class);
    $app->pipe(ErrorHandler::class);
    $app->pipe(Redirects::class);
    $app->pipe('/auth', [
        SessionMiddleware::class,
        OAuth2CallbackMiddleware::class
    ]);
    $app->pipe(\Zend\Expressive\Router\Middleware\RouteMiddleware::class);
    $app->pipe(\Zend\Expressive\Router\Middleware\ImplicitHeadMiddleware::class);
    $app->pipe(\Zend\Expressive\Router\Middleware\ImplicitOptionsMiddleware::class);
    $app->pipe(\Zend\Expressive\Router\Middleware\MethodNotAllowedMiddleware::class);
    $app->pipe(Helper\UrlHelperMiddleware::class);
    $app->pipe(\Zend\Expressive\Router\Middleware\DispatchMiddleware::class);
    $app->pipe(NotFoundHandler::class);
};
