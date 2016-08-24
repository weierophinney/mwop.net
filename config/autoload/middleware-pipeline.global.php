<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop;

use Zend\Expressive\Helper;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'dependencies' => [
        'factories' => [
            Auth\Middleware::class => Auth\MiddlewareFactory::class,
            ErrorHandler::class => Factory\ErrorHandlerFactory::class,
            Helper\UrlHelperMiddleware::class => Helper\UrlHelperMiddlewareFactory::class,
            Redirects::class => InvokableFactory::class,
            NotFound::class => Factory\NotFoundFactory::class,
            XClacksOverhead::class => InvokableFactory::class,
        ],
    ],
];
