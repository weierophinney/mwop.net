<?php
return [
    'blog' => [
        'db'            => 'sqlite:' . realpath(getcwd()) . '/data/posts.db',
        'cache_path'    => 'data/cache/posts',
        'cache_enabled' => true,
        'disqus'        => [
            'developer' => 0,
            'key'       => null,
        ],
    ],
    'contact' => [
        'recaptcha_pub_key'  => null,
        'recaptcha_priv_key' => null,
        'message' => [
            'to'   => null,
            'from' => null,
            'sender' => [
                'address' => null,
                'name'    => null,
            ],
        ],
    ],
    'debug' => false,
    'github' => [
        'user'  => 'weierophinney',
        'limit' => 4,
    ],
    'mail' => [
        'transport' => [
            'class' => 'Zend\Mail\Transport\Smtp',
            'options' => [
                'host' => null,
            ],
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
    'opauth' => [
        'path'               => '/auth/',
        'callback_url'       => '/auth/callback',
        'callback_transport' => 'session',
        'debug'              => false,
        'security_salt'      => 'PROVIDE A PROPER SALT',
        'Strategy'           => [
            'GitHub' => [
                'client_id'     => null,
                'client_secret' => null,
            ],
            'Google' => [
                'client_id'     => null,
                'client_secret' => null,
                'scope'         => 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email',
            ],
            'Twitter' => [
                'key'           => null,
                'secret'        => null,
            ],
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
    'phly-mustache' => [
        'paths' => [
            'blog'    => getcwd() . '/templates/blog',
            'contact' => getcwd() . '/templates/contact',
            'error'   => getcwd() . '/templates/error',
            'layout'  => getcwd() . '/templates/layout',
            'mwop'    => getcwd() . '/templates/mwop',
            [
                getcwd() . '/templates',
                getcwd() . '/data',
            ],
        ],
        'pragmas' => [
            'Phly\Mustache\Pragma\ContextualEscape',
            'Phly\Mustache\Pragma\ImplicitIterator',
        ],
    ],
    'zend-expressive' => [
        'error_handler' => [
            'template_404'   => 'error::404',
            'template_error' => 'error::500',
        ],
    ],

    // Trick zf-deploy into thinking this is a ZF2 app so it can build a package.
    'modules' => [],
];
