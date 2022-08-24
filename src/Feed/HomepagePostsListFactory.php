<?php

declare(strict_types=1);

namespace Mwop\Feed;

use CuyZ\Valinor\MapperBuilder;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Webmozart\Assert\Assert;

class HomepagePostsListFactory
{
    public function __invoke(ContainerInterface $container): HomepagePostsList
    {
        $config = $container->get('config-feeds');

        $logger = $container->get(LoggerInterface::class);
        Assert::isInstanceOf($logger, LoggerInterface::class);

        $builder = $container->get(MapperBuilder::class);
        Assert::isInstanceOf($builder, MapperBuilder::class);

        return new HomepagePostsList(
            limit: $config['feed-count'],
            logger: $logger,
            mapper: $builder->mapper(),
        );
    }
}
