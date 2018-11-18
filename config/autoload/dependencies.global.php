<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop;

use Phly\EventEmitter\ListenerProvider;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Zend\Diactoros\ResponseFactory;
use Zend\Expressive\Application;
use Zend\Expressive\Session\Cache\CacheSessionPersistence;
use Zend\Expressive\Session\SessionPersistenceInterface;
use Zend\Feed\Reader\Http\ClientInterface as FeedReaderHttpClientInterface;
use Zend\ServiceManager\Factory\InvokableFactory;

return ['dependencies' => [
    'aliases' => [
        SessionPersistenceInterface::class => CacheSessionPersistence::class,
    ],
    'delegators' => [
        Application::class => [
            Github\PuSH\RoutesDelegator::class,
        ],
        ListenerProvider::class => [
            Contact\SendMessageListenerDelegator::class,
        ],
    ],
    'invokables' => [
        Console\DockerCreateStack::class  => Console\DockerCreateStack::class,
        Console\DockerGetLatestTag::class => Console\DockerGetLatestTag::class,
        ResponseFactoryInterface::class   => ResponseFactory::class,
    ],
    'factories' => [
        'mail.transport'                  => Factory\MailTransport::class,
        Blog\BlogCachePool::class         => Blog\BlogCachePoolFactory::class,
        Blog\Console\ClearCache::class    => Blog\Console\ClearCacheFactory::class,
        Blog\Console\FeedGenerator::class => Blog\Console\FeedGeneratorFactory::class,
        Blog\Console\GenerateSearchData::class => InvokableFactory::class,
        Blog\Console\TagCloud::class      => Blog\Console\TagCloudFactory::class,
        Blog\Mapper::class                => Blog\MapperFactory::class,
        CacheItemPoolInterface::class     => Factory\CachePoolFactory::class,
        Console\ClearCache::class         => InvokableFactory::class,
        Console\CopyAssetSymlinks::class  => InvokableFactory::class,
        Console\CreateAssetSymlinks::class => InvokableFactory::class,
        Console\FeedAggregator::class     => Console\FeedAggregatorFactory::class,
        Console\UseDistTemplates::class   => InvokableFactory::class,
        Contact\SendMessageListener::class => Contact\SendMessageListenerFactory::class,
        FeedReaderHttpClientInterface::class => Feed\HttpPlugClientFactory::class,
        Github\AtomReader::class          => Github\AtomReaderFactory::class,
        Github\Console\Fetch::class       => Github\Console\FetchFactory::class,
        Github\PuSH\Logger::class         => Github\PuSH\LoggerFactory::class,
        Github\PuSH\LoggerAction::class   => Github\PuSH\LoggerActionFactory::class,
        OAuth2\ProviderFactory::class     => OAuth2\ProviderFactoryFactory::class,
        SessionCachePool::class           => Factory\SessionCachePoolFactory::class,
    ],
]];
