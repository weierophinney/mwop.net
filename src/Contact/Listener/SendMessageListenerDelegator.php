<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\Contact\Listener;

use Mwop\Contact\ContactMessage;
use Psr\Container\ContainerInterface;
use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;

class SendMessageListenerDelegator
{
    public function __invoke(ContainerInterface $container, $serviceName, callable $callback) : AttachableListenerProvider
    {
        $provider = $callback();
        $provider->listen(
            ContactMessage::class,
            $container->get(SendMessageListener::class)
        );
        return $provider;
    }
}
