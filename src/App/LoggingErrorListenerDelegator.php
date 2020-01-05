<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\App;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Laminas\Stratigility\Middleware\ErrorHandler;

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
