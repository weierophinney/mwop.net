<?php
use Mwop\Auth;
use Mwop\Blog;
use Mwop\Console;
use Mwop\Factory;
use Mwop\Github;
use Zend\Expressive\Application;
use Zend\Expressive\Container\ApplicationFactory;
use Zend\Expressive\Container\TemplatedErrorHandlerFactory;

return ['dependencies' => [
    'invokables' => [
        Console\PrepPageCacheRules::class => Console\PrepPageCacheRules::class,
    ],
    'factories' => [
        'http'                            => Factory\HttpClient::class,
        'mail.transport'                  => Factory\MailTransport::class,
        'session'                         => Factory\Session::class,
        Auth\AuthCallback::class          => Auth\AuthCallbackFactory::class,
        Auth\Auth::class                  => Auth\AuthFactory::class,
        Auth\Logout::class                => Auth\LogoutFactory::class,
        Auth\UserSession::class           => Auth\UserSessionFactory::class,
        Blog\Console\CachePosts::class    => Blog\Console\CachePostsFactory::class,
        Blog\Console\FeedGenerator::class => Blog\Console\FeedGeneratorFactory::class,
        Blog\Console\TagCloud::class      => Blog\Console\TagCloudFactory::class,
        Blog\Mapper::class                => Blog\MapperFactory::class,
        Github\AtomReader::class          => Github\AtomReaderFactory::class,
        Github\Console\Fetch::class       => Github\Console\FetchFactory::class,
        Application::class                => ApplicationFactory::class,
        'Zend\Expressive\FinalHandler'    => TemplatedErrorHandlerFactory::class,
    ],
]];
