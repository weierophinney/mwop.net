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
        'token' => null,
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
    'services' => [
        'invokables' => [
            'Mwop\BodyParams',
            'Mwop\NotAllowed',
            'Mwop\QueryParams',
            'Mwop\Blog\SeedBlogDatabase',
            'Mwop\Redirects',
        ],
        'factories' => [
            'http'                      => 'Mwop\Factory\HttpClient',
            'mail.transport'            => 'Mwop\Factory\MailTransport',
            'renderer'                  => 'Mwop\Factory\Renderer',
            'session'                   => 'Mwop\Factory\Session',
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
            'Mwop\HomePage'             => 'Mwop\Factory\HomePage',
            'Mwop\ResumePage'           => 'Mwop\Factory\ResumePage',
            'Mwop\Unauthorized'         => 'Mwop\Factory\Unauthorized',
            'Mwop\User\AuthCallback'    => 'Mwop\User\AuthCallbackFactory',
            'Mwop\User\Auth'            => 'Mwop\User\AuthFactory',
            'Mwop\User\Logout'          => 'Mwop\User\LogoutFactory',
            'Mwop\User\Middleware'      => 'Mwop\User\MiddlewareFactory',
            'Mwop\User\UserSession'     => 'Mwop\User\UserSessionFactory',
        ],
    ],
];
