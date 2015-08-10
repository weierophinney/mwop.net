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
                'path'            => '/blog',
                'middleware'      => 'Mwop\Blog\Middleware',
            ],
            [
                'middleware' => 'Mwop\Auth\Middleware',
                'path'       => '/auth',
            ],
            [
                'middleware' => 'Mwop\Contact\Middleware',
                'path'       => '/contact',
            ],
            [
                'middleware' => 'Mwop\Job\Middleware',
                'path'       => '/jobs',
            ],
            ['middleware' => 'Mwop\Unauthorized', 'error' => true],
            ['middleware' => 'Mwop\NotAllowed', 'error' => true],
            ['middleware' => 'Mwop\NotFound', 'error' => true],
            ['middleware' => 'Mwop\ErrorHandler', 'error' => true],
        ],
    ],
    'opauth' => [
        'path' => '/auth/',
        'callback_url' => '/auth/callback',
        'callback_transport' => 'session',
        'debug' => false,
        'security_salt' => 'PROVIDE A PROPER SALT',
        'Strategy' => [
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
                'key'    => null,
                'secret' => null,
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
    'services' => [
        'delegators' => [
            'Mwop\Blog\DisplayPostMiddleware' => [
                'Mwop\Blog\CachingDelegatorFactory',
            ],
        ],
        'invokables' => [
            'Mwop\Blog\FeedMiddleware'           => 'Mwop\Blog\FeedMiddleware',
            'Mwop\Blog\Console\SeedBlogDatabase' => 'Mwop\Blog\Console\SeedBlogDatabase',
            'Mwop\BodyParams'                    => 'Mwop\BodyParams',
            'Mwop\Console\PrepPageCacheRules'    => 'Mwop\Console\PrepPageCacheRules',
            'Mwop\NotAllowed'                    => 'Mwop\NotAllowed',
            'Mwop\NotFound'                      => 'Mwop\NotFound',
            'Mwop\Redirects'                     => 'Mwop\Redirects',
        ],
        'factories' => [
            'http'                            => 'Mwop\Factory\HttpClient',
            'mail.transport'                  => 'Mwop\Factory\MailTransport',
            'session'                         => 'Mwop\Factory\Session',
            'Mwop\Auth\AuthCallback'          => 'Mwop\Auth\AuthCallbackFactory',
            'Mwop\Auth\Auth'                  => 'Mwop\Auth\AuthFactory',
            'Mwop\Auth\Logout'                => 'Mwop\Auth\LogoutFactory',
            'Mwop\Auth\Middleware'            => 'Mwop\Auth\MiddlewareFactory',
            'Mwop\Auth\UserSession'           => 'Mwop\Auth\UserSessionFactory',
            'Mwop\Blog\Console\CachePosts'    => 'Mwop\Blog\Console\CachePostsFactory',
            'Mwop\Blog\Console\FeedGenerator' => 'Mwop\Blog\Console\FeedGeneratorFactory',
            'Mwop\Blog\Console\TagCloud'      => 'Mwop\Blog\Console\TagCloudFactory',
            'Mwop\Blog\DisplayPostMiddleware' => 'Mwop\Blog\DisplayPostMiddlewareFactory',
            'Mwop\Blog\ListPostsMiddleware'   => 'Mwop\Blog\ListPostsMiddlewareFactory',
            'Mwop\Blog\Mapper'                => 'Mwop\Blog\MapperFactory',
            'Mwop\Blog\Middleware'            => 'Mwop\Blog\MiddlewareFactory',
            'Mwop\ComicsPage'                 => 'Mwop\Factory\ComicsPage',
            'Mwop\Contact\LandingPage'        => 'Mwop\Contact\LandingPageFactory',
            'Mwop\Contact\Middleware'         => 'Mwop\Contact\MiddlewareFactory',
            'Mwop\Contact\Process'            => 'Mwop\Contact\ProcessFactory',
            'Mwop\Contact\ThankYouPage'       => 'Mwop\Contact\ThankYouPageFactory',
            'Mwop\ErrorHandler'               => 'Mwop\Factory\ErrorHandler',
            'Mwop\Github\AtomReader'          => 'Mwop\Github\AtomReaderFactory',
            'Mwop\Github\Console\Fetch'       => 'Mwop\Github\Console\FetchFactory',
            'Mwop\HomePage'                   => 'Mwop\Factory\PageFactory',
            'Mwop\Job\Middleware'             => 'Mwop\Job\MiddlewareFactory',
            'Mwop\ResumePage'                 => 'Mwop\Factory\PageFactory',
            'Mwop\Site'                       => 'Zend\Expressive\Container\ApplicationFactory',
            'Mwop\Template\TemplateInterface' => 'Mwop\Template\MustacheTemplateFactory',
            'Mwop\Unauthorized'               => 'Mwop\Factory\Unauthorized',
        ],
    ],
    // Trick zf-deploy into thinking this is a ZF2 app so it can build a package.
    'modules' => [],
];
