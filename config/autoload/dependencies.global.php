<?php
return ['dependencies' => [
    'delegators' => [
        'Mwop\Blog\DisplayPostMiddleware' => [
            'Mwop\Blog\CachingDelegatorFactory',
        ],
    ],
    'invokables' => [
        'Mwop\Blog\FeedMiddleware'           => 'Mwop\Blog\FeedMiddleware',
        'Mwop\Blog\Console\SeedBlogDatabase' => 'Mwop\Blog\Console\SeedBlogDatabase',
        'Mwop\Console\PrepPageCacheRules'    => 'Mwop\Console\PrepPageCacheRules',
    ],
    'factories' => [
        'http'                            => 'Mwop\Factory\HttpClient',
        'mail.transport'                  => 'Mwop\Factory\MailTransport',
        'session'                         => 'Mwop\Factory\Session',
        'Mwop\Auth\AuthCallback'          => 'Mwop\Auth\AuthCallbackFactory',
        'Mwop\Auth\Auth'                  => 'Mwop\Auth\AuthFactory',
        'Mwop\Auth\Logout'                => 'Mwop\Auth\LogoutFactory',
        'Mwop\Auth\UserSession'           => 'Mwop\Auth\UserSessionFactory',
        'Mwop\Blog\Console\CachePosts'    => 'Mwop\Blog\Console\CachePostsFactory',
        'Mwop\Blog\Console\FeedGenerator' => 'Mwop\Blog\Console\FeedGeneratorFactory',
        'Mwop\Blog\Console\TagCloud'      => 'Mwop\Blog\Console\TagCloudFactory',
        'Mwop\Blog\DisplayPostMiddleware' => 'Mwop\Blog\DisplayPostMiddlewareFactory',
        'Mwop\Blog\ListPostsMiddleware'   => 'Mwop\Blog\ListPostsMiddlewareFactory',
        'Mwop\Blog\Mapper'                => 'Mwop\Blog\MapperFactory',
        'Mwop\Contact\LandingPage'        => 'Mwop\Contact\LandingPageFactory',
        'Mwop\Contact\Process'            => 'Mwop\Contact\ProcessFactory',
        'Mwop\Contact\ThankYouPage'       => 'Mwop\Contact\ThankYouPageFactory',
        'Mwop\Github\AtomReader'          => 'Mwop\Github\AtomReaderFactory',
        'Mwop\Github\Console\Fetch'       => 'Mwop\Github\Console\FetchFactory',
        'Mwop\Job\GithubFeed'             => 'Mwop\Job\GithubFeedFactory',
        'Mwop\Site'                       => 'Zend\Expressive\Container\ApplicationFactory',
        'Zend\Expressive\FinalHandler'    => 'Zend\Expressive\Container\TemplatedErrorHandlerFactory',
    ],
]];
