<?php
use Mwop\Auth\Middleware as AuthMiddleware;
use Mwop\Auth\MiddlewareFactory as AuthMiddlewareFactory;
use Mwop\BodyParams;
use Mwop\Factory\Unauthorized as UnauthorizedFactory;
use Mwop\Redirects;
use Mwop\Unauthorized;

return [
    'dependencies' => [
        'invokables' => [
            BodyParams::class => BodyParams::class,
            Redirects::class  => Redirects::class,
        ],
        'factories' => [
            AuthMiddleware::class => AuthMiddlewareFactory::class,
            Unauthorized::class   => UnauthorizedFactory::class,
        ],
    ],
    'middleware_pipeline' => [
        'pre_routing' => [
            ['middleware' => Redirects::class],
            ['middleware' => BodyParams::class],
        ],

        'post_routing' => [
            [
                'path'       => '/auth',
                'middleware' => AuthMiddleware::class,
            ],
            [
                'middleware' => Unauthorized::class,
                'error'      => true,
            ],
        ],
    ],
];
