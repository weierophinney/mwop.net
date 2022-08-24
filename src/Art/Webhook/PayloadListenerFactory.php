<?php

declare(strict_types=1);

namespace Mwop\Art\Webhook;

use CuyZ\Valinor\MapperBuilder;
use Mwop\App\HomePageCacheExpiration;
use Mwop\Art\PhotoMapper;
use Mwop\Art\PhotoStorage;
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
            photos: $container->get(PhotoStorage::class),
            mapper: $container->get(PhotoMapper::class),
            logger: $container->get(LoggerInterface::class),
            notifier: $container->get(ErrorNotifier::class),
            backup: $container->get(DatabaseBackup::class),
            expireHomePageCache: $container->get(HomePageCacheExpiration::class),
            dataMapper: $builder->mapper(),
        );
    }
}
