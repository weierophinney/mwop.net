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
        ],
        'post_routing' => [
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
            'path'            => '/blog',
            'middleware'      => 'Mwop\Blog\Middleware',
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
        'invokables' => [
            'Mwop\Blog\SeedBlogDatabase' => 'Mwop\Blog\SeedBlogDatabase',
            'Mwop\BodyParams'            => 'Mwop\BodyParams',
            'Mwop\NotAllowed'            => 'Mwop\NotAllowed',
            'Mwop\NotFound'              => 'Mwop\NotFound',
            'Mwop\Redirects'             => 'Mwop\Redirects',
        ],
        'factories' => [
            'http'                      => 'Mwop\Factory\HttpClient',
            'mail.transport'            => 'Mwop\Factory\MailTransport',
            'renderer'                  => 'Mwop\Factory\Renderer',
            'session'                   => 'Mwop\Factory\Session',
            'Mwop\Auth\AuthCallback'    => 'Mwop\Auth\AuthCallbackFactory',
            'Mwop\Auth\Auth'            => 'Mwop\Auth\AuthFactory',
            'Mwop\Auth\Logout'          => 'Mwop\Auth\LogoutFactory',
            'Mwop\Auth\Middleware'      => 'Mwop\Auth\MiddlewareFactory',
            'Mwop\Auth\UserSession'     => 'Mwop\Auth\UserSessionFactory',
            'Mwop\Blog\CachingMiddleware'=> 'Mwop\Blog\CachingMiddlewareFactory',
            'Mwop\Blog\EngineMiddleware'=> 'Mwop\Blog\EngineMiddlewareFactory',
            'Mwop\Blog\FeedGenerator'   => 'Mwop\Blog\FeedGeneratorFactory',
            'Mwop\Blog\Mapper'          => 'Mwop\Blog\MapperFactory',
            'Mwop\Blog\Middleware'      => 'Mwop\Blog\MiddlewareFactory',
            'Mwop\Blog\TagCloud'        => 'Mwop\Blog\TagCloudFactory',
            'Mwop\CachePosts'           => 'Mwop\Factory\CachePosts',
            'Mwop\ComicsPage'           => 'Mwop\Factory\ComicsPage',
            'Mwop\Contact\LandingPage'  => 'Mwop\Contact\LandingPageFactory',
            'Mwop\Contact\Middleware'   => 'Mwop\Contact\MiddlewareFactory',
            'Mwop\Contact\Process'      => 'Mwop\Contact\ProcessFactory',
            'Mwop\Contact\ThankYouPage' => 'Mwop\Contact\ThankYouPageFactory',
            'Mwop\ErrorHandler'         => 'Mwop\Factory\ErrorHandler',
            'Mwop\Github\AtomReader'    => 'Mwop\Github\AtomReaderFactory',
            'Mwop\Github\Fetch'         => 'Mwop\Github\FetchFactory',
            'Mwop\HomePage'             => 'Mwop\Factory\PageFactory',
            'Mwop\Job\Middleware'       => 'Mwop\Job\MiddlewareFactory',
            'Mwop\ResumePage'           => 'Mwop\Factory\PageFactory',
            'Mwop\Site'                 => 'Zend\Expressive\Container\ApplicationFactory',
            'Mwop\Templated'            => 'Mwop\Factory\Templated',
            'Mwop\Template\TemplateInterface' => 'Mwop\Template\MustacheTemplateFactory',
            'Mwop\Unauthorized'         => 'Mwop\Factory\Unauthorized',
        ],
    ],
    // Trick zf-deploy into thinking this is a ZF2 app so it can build a package.
    'modules' => [],
];
