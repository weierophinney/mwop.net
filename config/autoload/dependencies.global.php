<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop;

use Zend\Expressive\Application;
use Zend\Expressive\Container\ApplicationFactory;
use Zend\Expressive\FinalHandler;
use Zend\Expressive\Helper;
use Zend\Feed\Reader\Http\ClientInterface as FeedReaderHttpClientInterface;
use Zend\ServiceManager\Factory\InvokableFactory;

return ['dependencies' => [
    'factories' => [
        'mail.transport'                  => Factory\MailTransport::class,
        'session'                         => Factory\Session::class,
        Application::class                => ApplicationFactory::class,
        Auth\AuthCallback::class          => Auth\AuthCallbackFactory::class,
        Auth\Auth::class                  => Auth\AuthFactory::class,
        Auth\Logout::class                => Auth\LogoutFactory::class,
        Auth\UserSession::class           => Auth\UserSessionFactory::class,
        Blog\Console\CachePosts::class    => Blog\Console\CachePostsFactory::class,
        Blog\Console\FeedGenerator::class => Blog\Console\FeedGeneratorFactory::class,
        Blog\Console\GenerateSearchData::class => InvokableFactory::class,
        Blog\Console\TagCloud::class      => Blog\Console\TagCloudFactory::class,
        Blog\Mapper::class                => Blog\MapperFactory::class,
        Console\CreateAssetSymlinks::class => InvokableFactory::class,
        Console\FeedAggregator::class     => Console\FeedAggregatorFactory::class,
        Console\UseDistTemplates::class   => InvokableFactory::class,
        FeedReaderHttpClientInterface::class => Feed\HttpPlugClientFactory::class,
        FinalHandler::class               => Factory\FinalHandlerFactory::class,
        Github\AtomReader::class          => Github\AtomReaderFactory::class,
        Github\Console\Fetch::class       => Github\Console\FetchFactory::class,
        Helper\UrlHelper::class           => Helper\UrlHelperFactory::class,
        UnauthorizedResponseFactory::class => UnauthorizedResponseFactoryFactory::class,
    ],
]];
