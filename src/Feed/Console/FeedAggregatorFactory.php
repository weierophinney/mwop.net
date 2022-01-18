<?php

declare(strict_types=1);

namespace Mwop\Feed\Console;

use Laminas\Feed\Reader\Http\ClientInterface as FeedReaderHttpClientInterface;
use Laminas\Feed\Reader\Reader as FeedReader;
use Laminas\Feed\Reader\StandaloneExtensionManager;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestFactoryInterface;

class FeedAggregatorFactory
{
    public function __invoke(ContainerInterface $container): FeedAggregator
    {
        $http = $container->get(FeedReaderHttpClientInterface::class);
        FeedReader::setHttpClient($http);
        FeedReader::setExtensionManager(new StandaloneExtensionManager());

        $config = $container->get('config-homepage');

        return new FeedAggregator(
            feeds: $config['feeds'] ?? [],
            toRetrieve: $config['feed-count'] ?? 10,
            requestFactory: $container->get(RequestFactoryInterface::class),
        );
    }
}
