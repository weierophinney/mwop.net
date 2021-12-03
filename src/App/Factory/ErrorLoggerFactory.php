<?php // phpcs:disable Generic.PHP.DiscourageGoto.Found


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
        $logger = new Logger('mwopnet');
        $logger->pushHandler(new ErrorLogHandler(
            messageType: ErrorLogHandler::OPERATING_SYSTEM,
            level: Logger::DEBUG,
            bubble: true,
            expandNewLines: true,
        ));
        $logger->pushProcessor(new PsrLogMessageProcessor());
        return $logger;
    }
}
