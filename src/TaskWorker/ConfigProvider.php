<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\TaskWorker;

use Phly\EventEmitter\ListenerProvider;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\MessageNotifierInterface;
use Swoole\Http\Server as HttpServer;

class ConfigProvider
{
    public function __invoke()
    {
        return [
            'dependencies' => [
                'aliases' => [
                    ListenerProviderInterface::class => ListenerProvider::class,
                ],
                'invokables' => [
                    ListenerProvider::class => ListenerProvider::class,
                ],
                'factories' => [
                    MessageNotifierInterface::class => MessageNotifierFactory::class,
                    TaskWorker::class               => TaskWorkerFactory::class,
                ],
                'delegators' => [
                    HttpServer::class => [
                        TaskWorkerDelegator::class,
                    ],
                ],
            ],
        ];
    }
}
