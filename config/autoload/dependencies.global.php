<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop;

use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Psr\EventDispatcher\ListenerProviderInterface;
use Zend\Expressive\Container;
use Zend\Expressive\Helper;
use Zend\Expressive\Middleware\ErrorResponseGenerator;
use Zend\Expressive\Middleware\NotFoundHandler;
use Zend\Expressive\Router;
use Zend\Expressive\Session\Cache\CacheSessionPersistence;
use Zend\Expressive\Session\SessionPersistenceInterface;
use Zend\Stratigility\Middleware\ErrorHandler;
use Zend\Stratigility\Middleware\OriginalMessages;

return ['dependencies' => [
    'aliases' => [
        ListenerProviderInterface::class   => AttachableListenerProvider::class,
        SessionPersistenceInterface::class => CacheSessionPersistence::class,
    ],
    'invokables' => [
        Helper\BodyParamsMiddleware::class => Helper\BodyParamsMiddleware::class,
        OriginalMessages::class            => OriginalMessages::class,
        Router\RouterInterface::class      => Router\FastRouteRouter::class,
    ],
    'factories' => [
        ErrorHandler::class               => Container\ErrorHandlerFactory::class,
        ErrorResponseGenerator::class     => Container\ErrorResponseGeneratorFactory::class,
        Helper\UrlHelper::class           => Helper\UrlHelperFactory::class,
        Helper\UrlHelperMiddleware::class => Helper\UrlHelperMiddlewareFactory::class,
        NotFoundHandler::class            => Container\NotFoundHandlerFactory::class,
    ],
    'delegators' => [
        App\Handler\ComicsPageHandler::class => [
            App\Handler\ComicsPageHandlerAuthDelegator::class,
        ],
    ],
]];
