<?php

declare(strict_types=1);

namespace Mwop\App\Factory;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use ZendTech\ZendHQ\MonologHandler\ZendHQHandler;

use function assert;
use function fopen;
use function function_exists;
use function is_array;

final class LoggerFactory
{
    public function __invoke(ContainerInterface $container): LoggerInterface
    {
        $config = $container->get('config');
        assert(is_array($config));

        $isDebug = $config['debug'] ?? false;
        $isDebug = (bool) $isDebug;

        $logger = new Logger('mwopnet');
        $logger->pushHandler(new StreamHandler(
            stream: fopen('php://stdout', 'a'),
            level: $isDebug ? Logger::DEBUG : Logger::INFO,
            bubble: true,
            useLocking: false,
        ));
        $this->registerZendHQHandler($logger);
        $logger->pushProcessor(new PsrLogMessageProcessor());

        return $logger;
    }

    private function registerZendHQHandler(Logger $logger): void
    {
        if (! function_exists('monitor_custom_event')) {
            return;
        }

        $logger->pushHandler(new ZendHQHandler(level: Logger::ERROR));
    }
}
