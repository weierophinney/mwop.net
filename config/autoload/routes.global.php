<?php

return [
    'dependencies' => [
        'invokables' => [
            Zend\Expressive\Router\RouterInterface::class => Zend\Expressive\Router\FastRouteRouter::class,
        ],
        'factories' => [
            'Mwop\ComicsPage' => 'Mwop\Factory\ComicsPage',
            'Mwop\HomePage'   => 'Mwop\Factory\PageFactory',
            'Mwop\ResumePage' => 'Mwop\Factory\PageFactory',
        ],
    ],

    'routes' => [
        [
            'path'            => '/',
            'middleware'      => 'Mwop\HomePage',
            'allowed_methods' => ['GET'],
        ],
        [
            'path'            => '/comics',
            'middleware'      => 'Mwop\ComicsPage',
            'allowed_methods' => ['GET'],
        ],
        [
            'path'            => '/resume',
            'middleware'      => 'Mwop\ResumePage',
            'allowed_methods' => ['GET'],
        ],
    ],
];
