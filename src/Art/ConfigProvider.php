<?php

declare(strict_types=1);

namespace Mwop\Art;

use Mezzio\Application;
use Mezzio\Authentication\AuthenticationMiddleware;
use Mezzio\Authorization\AuthorizationMiddleware;
use Mezzio\Helper\BodyParams\BodyParamsMiddleware;
use Mezzio\ProblemDetails\ProblemDetailsMiddleware;
use Mezzio\Session\SessionMiddleware;
use Mezzio\Swoole\Task\DeferredServiceListenerDelegator;
use Mwop\Art\Storage\PhotoRetrieval;
use Mwop\Art\Storage\PhotoRetrievalFactory;
use Mwop\Hooks\Middleware\ValidateWebhookRequestMiddleware;
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
        return [
            'database_filename'  => 'photos.db',
            'db'                 => [
                'dsn' => 'sqlite:' . realpath(getcwd()) . '/data/photos.db',
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
                AttachableListenerProvider::class => [
                    Webhook\PayloadListenerDelegator::class,
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

    public function registerRoutes(Application $app, string $basePath = ''): void
    {
        $app->get(
            $basePath . '/images/art/{type:fullsize|thumbnails}/{image:[^/ ]+.(?:png|jpg|jpeg|webp)}',
            Handler\ImageHandler::class,
            'art.image'
        );

        $app->get($basePath . '/art/', Handler\PhotosHandler::class, 'art.gallery');

        $app->get($basePath . '/art/{image:[^/]+\.(?:png|jpg|jpeg|webp)}/', Handler\PhotoHandler::class, 'art.photo');

        $app->get($basePath . '/art/photo/upload', [
            SessionMiddleware::class,
            AuthenticationMiddleware::class,
            AuthorizationMiddleware::class,
            Handler\UploadHandler::class,
        ], 'art.photo.upload');
        $app->post($basePath . '/art/photo/upload/process', [
            SessionMiddleware::class,
            AuthenticationMiddleware::class,
            AuthorizationMiddleware::class,
            BodyParamsMiddleware::class,
            Handler\ProcessUploadHandler::class,
        ], 'art.photo.upload.process');

        $app->post($basePath . '/api/art/new-photo', [
            ProblemDetailsMiddleware::class,
            ValidateWebhookRequestMiddleware::class,
            Handler\NewImageHandler::class,
        ], 'api.hook.instagram');
    }
}
