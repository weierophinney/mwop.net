<?php //phpcs:disable Generic.PHP.DiscourageGoto.Found

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

use function fopen;

class AccessLoggerFactory
{
    public function __invoke(ContainerInterface $container): LoggerInterface
    {
        $config  = $container->get('config');
        $isDebug = (bool) $config['debug'];

        $logger = new Logger('mwopnet');
        $logger->pushHandler(new StreamHandler(
            stream: fopen('/proc/self/fd/2', 'ab+'),
            level: $isDebug ? Logger::DEBUG : Logger::WARNING,
            bubble: true,
        ));
        $logger->pushProcessor(new PsrLogMessageProcessor());
        return $logger;
    }
}
