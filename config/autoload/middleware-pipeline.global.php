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
    'middleware_pipeline' => [
        'always' => [
            'middleware' => [
                XClacksOverhead::class,
                Redirects::class,
            ],
            'priority' => 10000,
        ],

        'auth' => [
            'path'       => '/auth',
            'middleware' => AuthMiddleware::class,
            'priority'   => 10,
        ],

        'routing' => [
            'middleware' => [
                ApplicationFactory::ROUTING_MIDDLEWARE,
                Helper\UrlHelperMiddleware::class,
                ApplicationFactory::DISPATCH_MIDDLEWARE,
            ],
            'priority' => 1,
        ],

        'error' => [
            'middleware' => [
                Unauthorized::class,
            ],
            'error' => true,
            'priority' => -10000,
        ],
    ],
];
