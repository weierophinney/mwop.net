<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\Contact;

use Psr\Container\ContainerInterface;
use Phly\EventEmitter\ListenerProvider;

class SendMessageListenerDelegator
{
    public function __invoke(ContainerInterface $container, $serviceName, callable $callback) : ListenerProvider
    {
        $provider = $callback();
        $provider->on(
            ContactMessage::class,
            $container->get(SendMessageListener::class)
        );
        return $provider;
    }
}
