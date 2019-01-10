<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\TaskWorker;

use Psr\Container\ContainerInterface;
use Swoole\Http\Server as HttpServer;

class QueueableListenerProviderFactory
{
    public function __invoke(ContainerInterface $container, string $serviceName = '') : QueueableListenerProvider
    {
        return new QueueableListenerProvider(
            $container->get(HttpServer::class)
        );
    }
}
