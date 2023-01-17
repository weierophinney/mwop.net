<?php

declare(strict_types=1);

namespace Mwop\Art;

use Mezzio\Application;
use Mezzio\Authentication\AuthenticationMiddleware;
use Mezzio\Authorization\AuthorizationMiddleware;
use Mezzio\Helper\BodyParams\BodyParamsMiddleware;
use Mezzio\ProblemDetails\ProblemDetailsMiddleware;
use Mezzio\Session\SessionMiddleware;
use Mwop\Hooks\Middleware\ValidateWebhookRequestMiddleware;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

final class RoutesDelegator
{
    public function __invoke(ContainerInterface $container, string $serviceName, callable $factory): Application
    {
        $basePath = '';
        $app      = $factory();
        Assert::isInstanceOf($app, Application::class);

        $app->get(
            $basePath . '/images/art/{type:fullsize|thumbnails}/{image:[^/ ]+.(?:png|jpg|jpeg|webp)}',
            Handler\ImageHandler::class,
            'art.image'
        );

        $app->get($basePath . '/art[/]', Handler\PhotosHandler::class, 'art.gallery');

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

        return $app;
    }
}
