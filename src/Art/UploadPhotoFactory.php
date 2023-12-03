<?php

declare(strict_types=1);

namespace Mwop\Art;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class UploadPhotoFactory
{
    public function __invoke(ContainerInterface $container): UploadPhoto
    {
        return new UploadPhoto(
            logger: $container->get(LoggerInterface::class),
            storage: $container->get(PhotoStorage::class),
            mapper: $container->get(PhotoMapper::class),
            backup: $container->get(Webhook\DatabaseBackup::class),
            form: new Form\UploadRuleSet(),
        );
    }
}
