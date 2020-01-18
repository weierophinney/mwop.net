<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\App\Factory;

use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class ErrorLoggerFactory
{
    public function __invoke(ContainerInterface $container): LoggerInterface
    {
        $logger             = new Logger('mwopnet');
        $logger->pushHandler(new ErrorLogHandler(
            ErrorLogHandler::OPERATING_SYSTEM,
            Logger::DEBUG,
            $bubble         = true,
            $expandNewLines = true
        ));
        $logger->pushProcessor(new PsrLogMessageProcessor());
        return $logger;
    }
}
