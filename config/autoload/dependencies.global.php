<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop;

use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Psr\Cache\CacheItemPoolInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Zend\Diactoros\ResponseFactory;
use Zend\Expressive\Application;
use Zend\Expressive\Session\Cache\CacheSessionPersistence;
use Zend\Expressive\Session\SessionPersistenceInterface;
use Zend\Feed\Reader\Http\ClientInterface as FeedReaderHttpClientInterface;
use Zend\ServiceManager\Factory\InvokableFactory;

return ['dependencies' => [
    'aliases' => [
        ListenerProviderInterface::class   => AttachableListenerProvider::class,
        SessionPersistenceInterface::class => CacheSessionPersistence::class,
    ],
    'delegators' => [
        Application::class => [
            Github\PuSH\RoutesDelegator::class,
        ],
        AttachableListenerProvider::class => [
            Contact\SendMessageListenerDelegator::class,
        ],
    ],
    'invokables' => [
        ResponseFactoryInterface::class => ResponseFactory::class,
    ],
    'factories' => [
        'mail.transport'                     => Factory\MailTransport::class,
        CacheItemPoolInterface::class        => Factory\CachePoolFactory::class,
        Console\ClearCache::class            => InvokableFactory::class,
        Console\CopyAssetSymlinks::class     => InvokableFactory::class,
        Console\CreateAssetSymlinks::class   => InvokableFactory::class,
        Console\FeedAggregator::class        => Console\FeedAggregatorFactory::class,
        Console\UseDistTemplates::class      => InvokableFactory::class,
        Contact\SendMessageListener::class   => Contact\SendMessageListenerFactory::class,
        EventDispatcherInterface::class      => Factory\EventDispatcherFactory::class,
        FeedReaderHttpClientInterface::class => Feed\HttpPlugClientFactory::class,
        Github\AtomReader::class             => Github\AtomReaderFactory::class,
        Github\Console\Fetch::class          => Github\Console\FetchFactory::class,
        Github\PuSH\Logger::class            => Github\PuSH\LoggerFactory::class,
        Github\PuSH\LoggerAction::class      => Github\PuSH\LoggerActionFactory::class,
        OAuth2\ProviderFactory::class        => OAuth2\ProviderFactoryFactory::class,
        SessionCachePool::class              => Factory\SessionCachePoolFactory::class,
    ],
]];
