<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop;

use Psr\Cache\CacheItemPoolInterface;
use Zend\Expressive\Application;
use Zend\Expressive\Session\SessionPersistenceInterface;
use Zend\Feed\Reader\Http\ClientInterface as FeedReaderHttpClientInterface;
use Zend\ServiceManager\Factory\InvokableFactory;

return ['dependencies' => [
    'delegators' => [
        Application::class => [
            Github\PuSH\RoutesDelegator::class,
        ],
    ],
    'factories' => [
        'mail.transport'                  => Factory\MailTransport::class,
        Blog\Console\CachePosts::class    => Blog\Console\CachePostsFactory::class,
        Blog\Console\FeedGenerator::class => Blog\Console\FeedGeneratorFactory::class,
        Blog\Console\GenerateSearchData::class => InvokableFactory::class,
        Blog\Console\TagCloud::class      => Blog\Console\TagCloudFactory::class,
        Blog\Mapper::class                => Blog\MapperFactory::class,
        CacheItemPoolInterface::class     => Factory\PredisCacheFactory::class,
        Console\CopyAssetSymlinks::class  => InvokableFactory::class,
        Console\CreateAssetSymlinks::class => InvokableFactory::class,
        Console\FeedAggregator::class     => Console\FeedAggregatorFactory::class,
        Console\UseDistTemplates::class   => InvokableFactory::class,
        FeedReaderHttpClientInterface::class => Feed\HttpPlugClientFactory::class,
        Github\AtomReader::class          => Github\AtomReaderFactory::class,
        Github\Console\Fetch::class       => Github\Console\FetchFactory::class,
        Github\PuSH\Logger::class         => Github\PuSH\LoggerFactory::class,
        Github\PuSH\LoggerAction::class   => Github\PuSH\LoggerActionFactory::class,
        SessionPersistenceInterface::class => Factory\CacheSessionPersistenceFactory::class,
    ],
]];
