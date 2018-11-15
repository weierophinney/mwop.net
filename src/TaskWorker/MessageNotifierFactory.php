<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\TaskWorker;

use Phly\EventEmitter\MessageNotifier;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

class MessageNotifierFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new MessageNotifier(
            $container->get(ListenerProviderInterface::class)
        );
    }
}
