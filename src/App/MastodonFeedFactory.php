<?php

declare(strict_types=1);

namespace Mwop\App;

use CuyZ\Valinor\MapperBuilder;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Webmozart\Assert\Assert;

class MastodonFeedFactory
{
    public function __invoke(ContainerInterface $container): MastodonFeed
    {
        /** @var MapperBuilder $builder */
        $builder = $container->get(MapperBuilder::class);
        Assert::isInstanceOf($builder, MapperBuilder::class);

        return new MastodonFeed(
            feedPath: 'data/mastodon.json',
            mapper: $builder->mapper(),
            logger: $container->get(LoggerInterface::class),
        );
    }
}
