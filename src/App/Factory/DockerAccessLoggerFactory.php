<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\App\Factory;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

use const STDOUT;

class DockerAccessLoggerFactory
{
    public function __invoke(ContainerInterface $container): LoggerInterface
    {
        $logger = new Logger('mwopnet');
        $logger->pushHandler(new StreamHandler(STDOUT, Logger::DEBUG));
        $logger->pushProcessor(new PsrLogMessageProcessor());
        return $logger;
    }
}
