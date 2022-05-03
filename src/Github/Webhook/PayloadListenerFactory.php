<?php

declare(strict_types=1);

namespace Mwop\Github\Webhook;

use Mwop\App\HomePageCacheExpiration;
use Mwop\Github\ItemList;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class PayloadListenerFactory
{
    public function __invoke(ContainerInterface $container): PayloadListener
    {
        return new PayloadListener(
            itemList: $container->get(ItemList::class),
            logger: $container->get(LoggerInterface::class),
            expireHomePageCache: $container->get(HomePageCacheExpiration::class),
        );
    }
}
