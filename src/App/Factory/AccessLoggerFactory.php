<?php //phpcs:disable Generic.PHP.DiscourageGoto.Found


declare(strict_types=1);

namespace Mwop\App\Factory;

use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class AccessLoggerFactory
{
    public function __invoke(ContainerInterface $container): LoggerInterface
    {
        $config  = $container->get('config');
        $isDebug = (bool) $config['debug'];

        $logger = new Logger('mwopnet');
        $logger->pushHandler(new ErrorLogHandler(
            messageType: ErrorLogHandler::SAPI,
            level: $isDebug ? Logger::DEBUG : Logger::INFO,
            bubble: true,
            expandNewlines: true,
        ));
        $logger->pushProcessor(new PsrLogMessageProcessor());
        return $logger;
    }
}
