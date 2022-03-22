<?php

declare(strict_types=1);

namespace Mwop\Art\Console;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Psr\Container\ContainerInterface;

use function basename;
use function dirname;

class FetchPhotoDatabaseFactory
{
    public function __invoke(ContainerInterface $container): FetchPhotoDatabase
    {
        $config = $container->get('config-art');

        return new FetchPhotoDatabase(
            app: new Filesystem(
                new LocalFilesystemAdapter(dirname($config['database_path'])),
            ),
            remote: $container->get('Mwop\Art\Storage\Images'),
            database: basename($config['database_path']),
        );
    }
}
