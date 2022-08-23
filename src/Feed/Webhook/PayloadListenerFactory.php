<?php

declare(strict_types=1);

namespace Mwop\Feed\Webhook;

use CuyZ\Valinor\MapperBuilder;
use Mwop\App\HomePageCacheExpiration;
use Mwop\Feed\HomepagePostsList;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Webmozart\Assert\Assert;

class PayloadListenerFactory
{
    public function __invoke(ContainerInterface $container): PayloadListener
    {
        $builder = $container->get(MapperBuilder::class);
        Assert::isInstanceOf($builder, MapperBuilder::class);

        return new PayloadListener(
            postsList: $container->get(HomepagePostsList::class),
            logger: $container->get(LoggerInterface::class),
            expireHomePageCache: $container->get(HomePageCacheExpiration::class),
            mapper: $builder->mapper(),
        );
    }
}
