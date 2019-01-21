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
        EventDispatcherInterface::class      => Factory\EventDispatcherFactory::class,
        FeedReaderHttpClientInterface::class => Feed\HttpPlugClientFactory::class,
        SessionCachePool::class              => Factory\SessionCachePoolFactory::class,
    ],
]];
