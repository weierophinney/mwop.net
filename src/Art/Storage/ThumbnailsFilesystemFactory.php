<?php

declare(strict_types=1);

namespace Mwop\Art\Storage;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\AwsS3V3\PortableVisibilityConverter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\Visibility;
use Psr\Container\ContainerInterface;

class ThumbnailsFilesystemFactory
{
    public function __invoke(ContainerInterface $container): FilesystemOperator
    {
        $folder = $container->get('config-art')['storage']['folder'] ?? '';
        return new Filesystem(
            new AwsS3V3Adapter(
                $container->get(S3Client::class),
                $container->get('config-file-storage')['bucket'] ?? '',
                $folder . '/thumbs/',
                new PortableVisibilityConverter(
                    Visibility::PRIVATE
                ),
            )
        );
    }
}
