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
        // Timeout after 3s to prevent database locks
        $pdo->setAttribute(PDO::ATTR_TIMEOUT, 3);
        $pdo->exec('PRAGMA journal_mode=WAL;');

        return new PdoPhotoMapper($pdo);
    }
}
