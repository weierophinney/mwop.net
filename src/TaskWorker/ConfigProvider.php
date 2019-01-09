<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\TaskWorker;

use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Swoole\Http\Server as HttpServer;

class ConfigProvider
{
    public function __invoke()
    {
        return [
            'dependencies' => [
                'aliases' => [
                    ListenerProviderInterface::class => AttachableListenerProvider::class,
                ],
                'invokables' => [
                    AttachableListenerProvider::class => AttachableListenerProvider::class,
                ],
                'factories' => [
                    EventDispatcherInterface::class => EventDispatcherFactory::class,
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
