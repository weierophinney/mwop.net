<?php

declare(strict_types=1);

namespace Mwop\Art\Storage;

use Aws\S3\S3Client;
use Psr\Container\ContainerInterface;

class S3ClientFactory
{
    public function __invoke(ContainerInterface $container): S3Client
    {
        $config = $container->get('config-art');

        return new S3Client([
            'version'     => 'latest',
            'endpoint'    => $config['storage']['endpoint'],
            'region'      => $config['storage']['region'],
            'credentials' => [
                'key'    => $config['storage']['key'],
                'secret' => $config['storage']['secret'],
            ],
        ]);
    }
}
