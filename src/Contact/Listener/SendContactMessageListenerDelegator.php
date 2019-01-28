<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\Contact\Listener;

use Mwop\Contact\SendContactMessageEvent;
use Psr\Container\ContainerInterface;
use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;

class SendContactMessageListenerDelegator
{
    public function __invoke(ContainerInterface $container, $serviceName, callable $callback) : AttachableListenerProvider
    {
        $provider = $callback();
        $provider->listen(
            SendContactMessageEvent::class,
            $container->get(SendContactMessageListener::class)
        );
        return $provider;
    }
}
