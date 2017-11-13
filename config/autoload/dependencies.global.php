<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop;

use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Zend\Expressive\Application;
use Zend\Expressive\Container;
use Zend\Expressive\Helper;
use Zend\Feed\Reader\Http\ClientInterface as FeedReaderHttpClientInterface;
use Zend\ServiceManager\Factory\InvokableFactory;

return ['dependencies' => [
    'delegators' => [
        Application::class => [
            Discourse\RoutesDelegator::class,
            Github\PuSH\RoutesDelegator::class,
        ],
    ],
    'factories' => [
        'mail.transport'                  => Factory\MailTransport::class,
        Application::class                => Container\ApplicationFactory::class,
        Auth\Auth::class                  => Auth\AuthFactory::class,
        Auth\OAuth2ProviderFactory::class => Auth\OAuth2ProviderFactoryFactory::class,
        Auth\Logout::class                => InvokableFactory::class,
        Auth\UserSession::class           => InvokableFactory::class,
        Blog\Console\CachePosts::class    => Blog\Console\CachePostsFactory::class,
        Blog\Console\FeedGenerator::class => Blog\Console\FeedGeneratorFactory::class,
        Blog\Console\GenerateSearchData::class => InvokableFactory::class,
        Blog\Console\TagCloud::class      => Blog\Console\TagCloudFactory::class,
        Blog\Mapper::class                => Blog\MapperFactory::class,
        Console\CopyAssetSymlinks::class  => InvokableFactory::class,
        Console\CreateAssetSymlinks::class => InvokableFactory::class,
        Console\FeedAggregator::class     => Console\FeedAggregatorFactory::class,
        Console\UseDistTemplates::class   => InvokableFactory::class,
        Discourse\Logger::class           => Discourse\LoggerFactory::class,
        Discourse\LoggerAction::class     => Discourse\LoggerActionFactory::class,
        FeedReaderHttpClientInterface::class => Feed\HttpPlugClientFactory::class,
        Github\AtomReader::class          => Github\AtomReaderFactory::class,
        Github\Console\Fetch::class       => Github\Console\FetchFactory::class,
        Github\PuSH\Logger::class         => Github\PuSH\LoggerFactory::class,
        Github\PuSH\LoggerAction::class   => Github\PuSH\LoggerActionFactory::class,
        LoggerInterface::class            => Factory\LoggerFactory::class,
        ResponseInterface::class          => Factory\ResponseFactory::class,
        UnauthorizedResponseFactory::class => UnauthorizedResponseFactoryFactory::class,
    ],
    'shared' => [
        ResponseInterface::class => false,
    ],
]];
