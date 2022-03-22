<?php

declare(strict_types=1);

namespace Mwop\Art;

use Psr\Container\ContainerInterface;

class PdoPhotoMapperFactory
{
    public function __invoke(ContainerInterface $container): PdoPhotoMapper
    {
        $config = $container->get('config-art');

        return new PdoPhotoMapper(
            new PDOConnection(dsn: $config['db']['dsn']),
        );
    }
}
