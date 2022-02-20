<?php

declare(strict_types=1);

namespace Mwop\Art;

use PDO;
use Psr\Container\ContainerInterface;

class PdoPhotoMapperFactory
{
    public function __invoke(ContainerInterface $container): PdoPhotoMapper
    {
        $config = $container->get('config-art');
        $pdo    = new PDO(dsn: $config['db']['dsn']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return new PdoPhotoMapper($pdo);
    }
}
