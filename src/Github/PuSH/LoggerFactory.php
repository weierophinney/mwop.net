<?php

namespace Mwop\Github\PuSH;

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Container\ContainerInterface;

class LoggerFactory
{
    public function __invoke(ContainerInterface $container) : Logger
    {
        $handler = new StreamHandler(realpath(getcwd()) . '/data/github-push.log', Logger::INFO);
        $handler->pushProcessor(new PsrLogMessageProcessor());
        $handler->setFormatter(new LineFormatter("[%datetime%] %message%\n", 'Y-m-d H:i:s'));

        $logger = new Logger('github');
        $logger->pushHandler($handler);
        return $logger;
    }
}
