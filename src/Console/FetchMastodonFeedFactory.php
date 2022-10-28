<?php

declare(strict_types=1);

namespace Mwop\Console;

use CuyZ\Valinor\MapperBuilder;
use Laminas\Feed\Reader\Http\ClientInterface as FeedReaderHttpClientInterface;
use Laminas\Feed\Reader\Reader as FeedReader;
use Laminas\Feed\Reader\StandaloneExtensionManager;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

class FetchMastodonFeedFactory
{
    public function __invoke(ContainerInterface $container): FetchMastodonFeed
    {
        /** @var MapperBuilder $builder */
        $builder = $container->get(MapperBuilder::class);
        Assert::isInstanceOf($builder, MapperBuilder::class);

        $http = $container->get(FeedReaderHttpClientInterface::class);
        FeedReader::setHttpClient($http);
        FeedReader::setExtensionManager(new StandaloneExtensionManager());

        return new FetchMastodonFeed(
            mapper: $builder->mapper(),
        );
    }
}
