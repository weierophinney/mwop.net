<?php
use Mwop\Auth\Middleware as AuthMiddleware;
use Mwop\Auth\MiddlewareFactory as AuthMiddlewareFactory;
use Mwop\ErrorHandler;
use Mwop\Factory\Unauthorized as UnauthorizedFactory;
use Mwop\NotFound;
use Mwop\Redirects;
use Mwop\Unauthorized;
use Mwop\XClacksOverhead;
use Zend\Expressive\Container\ApplicationFactory;
use Zend\Expressive\Helper;

return [
    'dependencies' => [
        'invokables' => [
            Redirects::class       => Redirects::class,
            XClacksOverhead::class => XClacksOverhead::class,
        ],
        'factories' => [
            AuthMiddleware::class => AuthMiddlewareFactory::class,
            Helper\UrlHelperMiddleware::class => Helper\UrlHelperMiddlewareFactory::class,
            Unauthorized::class => UnauthorizedFactory::class,
        ],
    ],
];
