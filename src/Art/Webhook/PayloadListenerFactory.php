<?php

declare(strict_types=1);

namespace Mwop\Art\Webhook;

use Mwop\App\HomePageCacheExpiration;
use Mwop\Art\PhotoMapper;
use Mwop\Art\PhotoStorage;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class PayloadListenerFactory
{
    public function __invoke(ContainerInterface $container): PayloadListener
    {
        return new PayloadListener(
            photos: $container->get(PhotoStorage::class),
            mapper: $container->get(PhotoMapper::class),
            logger: $container->get(LoggerInterface::class),
            notifier: $container->get(ErrorNotifier::class),
            backup: $container->get(DatabaseBackup::class),
            expireHomePageCache: $container->get(HomePageCacheExpiration::class),
        );
    }
}
