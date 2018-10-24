<?php

declare(strict_types=1);

namespace Mwop;

use Middlewares\Csp;
use Zend\Expressive\Helper\ServerUrlMiddleware;
use Zend\Expressive\Helper\UrlHelperMiddleware;
use Zend\Expressive\Handler\NotFoundHandler;
use Zend\Expressive\Router\Middleware\DispatchMiddleware;
use Zend\Expressive\Router\Middleware\ImplicitHeadMiddleware;
use Zend\Expressive\Router\Middleware\ImplicitOptionsMiddleware;
use Zend\Expressive\Router\Middleware\MethodNotAllowedMiddleware;
use Zend\Expressive\Router\Middleware\RouteMiddleware;
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
    $app->pipe(ServerUrlMiddleware::class);
    $app->pipe(Redirects::class);
    $app->pipe(RouteMiddleware::class);
    $app->pipe(ImplicitHeadMiddleware::class);
    $app->pipe(ImplicitOptionsMiddleware::class);
    $app->pipe(MethodNotAllowedMiddleware::class);
    $app->pipe(UrlHelperMiddleware::class);
    $app->pipe(DispatchMiddleware::class);
    $app->pipe(NotFoundHandler::class);
};
