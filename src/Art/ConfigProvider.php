<?php

declare(strict_types=1);

namespace Mwop\Art;

use Aws\S3\S3Client;
use Mezzio\Application;
use Mezzio\ProblemDetails\ProblemDetailsMiddleware;
use Mezzio\Swoole\Task\DeferredServiceListenerDelegator;
use Mwop\Hooks\Middleware\ValidateWebhookRequestMiddleware;
use Phly\ConfigFactory\ConfigFactory;
use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'art'          => $this->getConfig(),
            'dependencies' => $this->getDependencies(),
        ];
    }

    public function getConfig(): array
    {
        return [
            'database_filename'  => 'photos.db',
            'db' => [
                'dsn' => 'sqlite:' . realpath(getcwd()) . '/data/photos.db',
            ],
            'error_notification' => [
                'sender'    => '',
                'recipient' => '',
            ],
            'storage'            => [
                'endpoint' => '',
                'region'   => '',
                'bucket'   => '',
                'folder'   => '',
                'key'      => '',
                'secret'   => '',
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            'delegators' => [
                AttachableListenerProvider::class => [
                    Webhook\PayloadListenerDelegator::class,
                ],
                Webhook\PayloadListener::class    => [
                    DeferredServiceListenerDelegator::class,
                ],
            ],
            'factories'  => [
                'config-art'                   => ConfigFactory::class,
                Handler\ImageHandler::class    => Handler\ImageHandlerFactory::class,
                Handler\NewImageHandler::class => Handler\NewImageHandlerFactory::class,
                'Mwop\Art\Storage\Images'      => Storage\ImagesFilesystemFactory::class,
                'Mwop\Art\Storage\Thumbnails'  => Storage\ThumbnailsFilesystemFactory::class,
                PhotoMapper::class             => PdoPhotoMapperFactory::class,
                PhotoStorage::class            => PhotoStorageFactory::class,
                S3Client::class                => Storage\S3ClientFactory::class,
                Webhook\DatabaseBackup::class  => Webhook\DatabaseBackupFactory::class,
                Webhook\ErrorNotifier::class   => Webhook\ErrorNotifierFactory::class,
                Webhook\PayloadListener::class => Webhook\PayloadListenerFactory::class,
            ],
        ];
    }

    public function registerRoutes(Application $app, string $basePath = ''): void
    {
        $app->get(
            $basePath . '/images/art/{type:fullsize}/{image:[^/ ]+.(?:png|jpg|jpeg|webp)}',
            Handler\ImageHandler::class,
            'art.image.fullsize'
        );

        $app->get(
            $basePath . '/images/art/{type:thumbnails}/{image:[^/ ]+.(?:png|jpg|jpeg|webp)}',
            Handler\ImageHandler::class,
            'art.image.thumbnail'
        );

        $app->post($basePath . '/api/art/new-photo', [
            ProblemDetailsMiddleware::class,
            ValidateWebhookRequestMiddleware::class,
            Handler\NewImageHandler::class,
        ], 'api.hook.instagram');
    }
}
