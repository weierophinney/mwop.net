<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop;

use Middlewares\Csp;
use Zend\Expressive\Container;
use Zend\Expressive\Helper;
use Zend\Expressive\Middleware\ErrorResponseGenerator;
use Zend\Expressive\Middleware\NotFoundHandler;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\Stratigility\Middleware\ErrorHandler;
use Zend\Stratigility\Middleware\OriginalMessages;

return ['dependencies' => [
    'invokables' => [
        OriginalMessages::class => OriginalMessages::class,
    ],
    'factories' => [
        ErrorHandler::class               => Container\ErrorHandlerFactory::class,
        ErrorResponseGenerator::class     => Container\ErrorResponseGeneratorFactory::class,
        Helper\UrlHelper::class           => Helper\UrlHelperFactory::class,
        Helper\UrlHelperMiddleware::class => Helper\UrlHelperMiddlewareFactory::class,
        NotFoundHandler::class            => Container\NotFoundHandlerFactory::class,
    ],
]];
