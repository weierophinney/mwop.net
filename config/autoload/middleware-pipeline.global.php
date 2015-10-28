<?php

return [
    'dependencies' => [
        'invokables' => [
            'Mwop\BodyParams' => 'Mwop\BodyParams',
            'Mwop\Redirects'  => 'Mwop\Redirects',
        ],
        'factories' => [
            'Mwop\Auth\Middleware'    => 'Mwop\Auth\MiddlewareFactory',
            'Mwop\Blog\Middleware'    => 'Mwop\Blog\MiddlewareFactory',
            'Mwop\Contact\Middleware' => 'Mwop\Contact\MiddlewareFactory',
            'Mwop\Job\Middleware'     => 'Mwop\Job\MiddlewareFactory',
            'Mwop\Unauthorized'       => 'Mwop\Factory\Unauthorized',
        ],
    ],
    'middleware_pipeline' => [
        'pre_routing' => [
            ['middleware' => 'Mwop\Redirects'],
            ['middleware' => 'Mwop\BodyParams'],
        ],

        'post_routing' => [
            [
                'path'       => '/blog',
                'middleware' => 'Mwop\Blog\Middleware',
            ],
            [
                'path'       => '/auth',
                'middleware' => 'Mwop\Auth\Middleware',
            ],
            [
                'path'       => '/contact',
                'middleware' => 'Mwop\Contact\Middleware',
            ],
            [
                'path'       => '/jobs',
                'middleware' => 'Mwop\Job\Middleware',
            ],
            [
                'middleware' => 'Mwop\Unauthorized',
                'error'      => true,
            ],
        ],
    ],
];
