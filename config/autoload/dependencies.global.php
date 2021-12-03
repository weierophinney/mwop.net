<?php

declare(strict_types=1);

namespace Mwop;

use Laminas\Stratigility\Middleware\ErrorHandler;
use Mezzio\Container;
use Mezzio\Helper;
use Mezzio\Middleware\ErrorResponseGenerator;
use Mezzio\Middleware\NotFoundHandler;
use Mezzio\Router;
use Mezzio\Session\Cache\CacheSessionPersistence;
use Mezzio\Session\SessionPersistenceInterface;
use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Psr\EventDispatcher\ListenerProviderInterface;

return [
    'dependencies' => [
        'aliases'    => [
            ListenerProviderInterface::class   => AttachableListenerProvider::class,
            SessionPersistenceInterface::class => CacheSessionPersistence::class,
        ],
        'invokables' => [
            Helper\BodyParamsMiddleware::class => Helper\BodyParamsMiddleware::class,
            Router\RouterInterface::class      => Router\FastRouteRouter::class,
        ],
        'factories'  => [
            ErrorHandler::class               => Container\ErrorHandlerFactory::class,
            ErrorResponseGenerator::class     => Container\ErrorResponseGeneratorFactory::class,
            Helper\UrlHelper::class           => Helper\UrlHelperFactory::class,
            Helper\UrlHelperMiddleware::class => Helper\UrlHelperMiddlewareFactory::class,
            NotFoundHandler::class            => Container\NotFoundHandlerFactory::class,
        ],
        /*
        'delegators' => [
            App\Handler\ComicsPageHandler::class => [
                App\Handler\ComicsPageHandlerAuthDelegator::class,
            ],
        ],
         */
    ],
];
