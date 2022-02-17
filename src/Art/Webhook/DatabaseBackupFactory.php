<?php

declare(strict_types=1);

namespace Mwop\Art\Webhook;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Psr\Container\ContainerInterface;

use function getcwd;
use function realpath;

class DatabaseBackupFactory
{
    public function __invoke(ContainerInterface $container): DatabaseBackup
    {
        $config = $container->get('config-art');

        return new DatabaseBackup(
            application: new Filesystem(
                new LocalFilesystemAdapter(realpath(getcwd()) . '/data'),
            ),
            remote: $container->get('Mwop\Art\Storage\Images'),
            database: $config['database_filename'],
        );
    }
}
