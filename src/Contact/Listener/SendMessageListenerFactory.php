<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\Contact\Listener;

use Phly\Swoole\TaskWorker\QueueableListener;
use Psr\Container\ContainerInterface;
use RuntimeException;
use Swift_Mailer as Mailer;

class SendMessageListenerFactory
{
    public function __invoke(ContainerInterface $container) : callable
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $config = $config['contact']['message'] ?? [];

        if (! isset($config['to'])
            || ! isset($config['sender']['address'])
        ) {
            $baseConfigKey = 'contact.message';
            throw new RuntimeException(sprintf(
                'Cannot create %s; missing required config structure.'
                . ' Requires each of: %s.to and %s.sender.address',
                SendMessageListener::class,
                $baseConfigKey,
                $baseConfigKey
            ));
        }

        return new SendMessageListener(
            $container->get('mail.transport'),
            $config
        );
    }
}
