<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Factory;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Mwop\LoggingErrorListener;
use Zend\Stratigility\Middleware\ErrorHandler;

class LoggingErrorListenerDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $callback
    ) : ErrorHandler {
        $errorHandler = $callback();
        $errorHandler->attachListener(
            new LoggingErrorListener($container->get(LoggerInterface::class))
        );
        return $errorHandler;
    }
}
