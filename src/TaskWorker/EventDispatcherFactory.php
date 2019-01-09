<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\TaskWorker;

use Phly\EventDispatcher\EventDispatcher;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

class EventDispatcherFactory
{
    public function __invoke(ContainerInterface $container) : EventDispatcherInterface
    {
        return new EventDispatcher(
            $container->get(ListenerProviderInterface::class)
        );
    }
}
