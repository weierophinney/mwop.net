<?php

declare(strict_types=1);

namespace Mwop\Art;

use CuyZ\Valinor\MapperBuilder;
use Mezzio\Application;
use Mezzio\Swoole\Task\DeferredServiceListenerDelegator;
use Mwop\Art\Storage\PhotoRetrieval;
use Mwop\Art\Storage\PhotoRetrievalFactory;
use Phly\ConfigFactory\ConfigFactory;
use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;

use function getcwd;
use function realpath;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'art'                       => $this->getConfig(),
            'dependencies'              => $this->getDependencies(),
            'mezzio-authorization-rbac' => $this->getRbac(),
            'templates'                 => $this->getTemplateConfig(),
        ];
    }

    public function getConfig(): array
    {
        $databasePath = realpath(getcwd()) . '/data/photodb/photos.db';
        return [
            'database_path'      => $databasePath,
            'db'                 => [
                'dsn' => 'sqlite:' . $databasePath,
            ],
            'error_notification' => [
                'sender'    => '',
                'recipient' => '',
            ],
            'per_page'           => 24,
            'storage'            => [
                'folder' => '',
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            'delegators' => [
                Application::class                => [
                    RoutesDelegator::class,
                ],
                AttachableListenerProvider::class => [
                    Webhook\PayloadListenerDelegator::class,
                ],
                MapperBuilder::class              => [
                    MapperBuilderDelegator::class,
                ],
                Webhook\PayloadListener::class    => [
                    DeferredServiceListenerDelegator::class,
                ],
            ],
            'factories'  => [
                'config-art'                        => ConfigFactory::class,
                Console\FetchPhotoDatabase::class   => Console\FetchPhotoDatabaseFactory::class,
                Handler\ImageHandler::class         => Handler\ImageHandlerFactory::class,
                Handler\NewImageHandler::class      => Handler\NewImageHandlerFactory::class,
                Handler\PhotoHandler::class         => Handler\PhotoHandlerFactory::class,
                Handler\PhotosHandler::class        => Handler\PhotosHandlerFactory::class,
                Handler\ProcessUploadHandler::class => Handler\ProcessUploadHandlerFactory::class,
                Handler\UploadHandler::class        => Handler\UploadHandlerFactory::class,
                'Mwop\Art\Storage\Images'           => Storage\ImagesFilesystemFactory::class,
                'Mwop\Art\Storage\Thumbnails'       => Storage\ThumbnailsFilesystemFactory::class,
                PhotoMapper::class                  => PdoPhotoMapperFactory::class,
                PhotoRetrieval::class               => PhotoRetrievalFactory::class,
                PhotoStorage::class                 => PhotoStorageFactory::class,
                PostToMastodon::class               => PostToMastodonFactory::class,
                UploadPhoto::class                  => UploadPhotoFactory::class,
                Webhook\DatabaseBackup::class       => Webhook\DatabaseBackupFactory::class,
                Webhook\ErrorNotifier::class        => Webhook\ErrorNotifierFactory::class,
                Webhook\PayloadListener::class      => Webhook\PayloadListenerFactory::class,
            ],
        ];
    }

    public function getRbac(): array
    {
        return [
            'roles'       => [
                'admin' => [],
            ],
            'permissions' => [
                'admin' => [
                    'art.photo.upload',
                    'art.photo.upload.process',
                ],
            ],
        ];
    }

    public function getTemplateConfig(): array
    {
        return [
            'paths' => [
                'art' => [__DIR__ . '/templates'],
            ],
        ];
    }
}
