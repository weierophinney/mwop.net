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
            'body-params'                => 'Mwop\BodyParams',
            'not-allowed'                => 'Mwop\NotAllowed',
            'query-params'               => 'Mwop\QueryParams',
            'Mwop\Blog\SeedBlogDatabase' => 'Mwop\Blog\SeedBlogDatabase',
            'Mwop\Redirects'             => 'Mwop\Redirects',
        ],
        'factories' => [
            'contact'                   => 'Mwop\Contact\Factory\Contact',
            'contact.landing'           => 'Mwop\Contact\Factory\LandingPage',
            'contact.process'           => 'Mwop\Contact\Factory\Process',
            'contact.thankyou'          => 'Mwop\Contact\Factory\ThankYouPage',
            'http'                      => 'Mwop\Factory\HttpClient',
            'mail.transport'            => 'Mwop\Factory\MailTransport',
            'page.home'                 => 'Mwop\Factory\HomePage',
            'page.resume'               => 'Mwop\Factory\ResumePage',
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
            'Mwop\ErrorHandler'         => 'Mwop\Factory\ErrorHandler',
            'Mwop\Github\AtomReader'    => 'Mwop\Factory\AtomReader',
            'Mwop\Github\Fetch'         => 'Mwop\Factory\GithubFetch',
            'Mwop\Unauthorized'         => 'Mwop\Factory\Unauthorized',
            'Mwop\User\Auth'            => 'Mwop\User\AuthFactory',
            'Mwop\User\AuthCallback'    => 'Mwop\User\AuthCallbackFactory',
            'Mwop\User\Logout'          => 'Mwop\User\LogoutFactory',
            'Mwop\User\Middleware'      => 'Mwop\User\MiddlewareFactory',
            'Mwop\User\UserSession'     => 'Mwop\User\UserSessionFactory',
        ],
    ],
];
