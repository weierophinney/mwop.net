<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Contact\Listener;

use Mwop\Contact\SendContactMessageEvent;
use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Psr\Container\ContainerInterface;

class SendContactMessageListenerDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $callback
    ): AttachableListenerProvider {
        $provider = $callback();
        $provider->listen(
            SendContactMessageEvent::class,
            $container->get(SendContactMessageListener::class)
        );
        return $provider;
    }
}
