<?php

declare(strict_types=1);

namespace Mwop\Feed\Webhook;

use Mwop\App\HomePageCacheExpiration;
use Mwop\Feed\HomepagePostsList;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class PayloadListenerFactory
{
    public function __invoke(ContainerInterface $container): PayloadListener
    {
        return new PayloadListener(
            postsList: $container->get(HomepagePostsList::class),
            logger: $container->get(LoggerInterface::class),
            expireHomePageCache: $container->get(HomePageCacheExpiration::class),
        );
    }
}
