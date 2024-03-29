<?php

declare(strict_types=1);

namespace Mwop\Art\Storage;

use Aws\S3\S3Client;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class PhotoRetrievalFactory
{
    public function __invoke(ContainerInterface $container): PhotoRetrieval
    {
        return new PhotoRetrieval(
            responseFactory: $container->get(ResponseFactoryInterface::class),
            client: $container->get(S3Client::class),
            bucket: $container->get('config-file-storage')['bucket'] ?? '',
        );
    }
}
