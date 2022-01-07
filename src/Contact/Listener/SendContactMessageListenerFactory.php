<?php // phpcs:disable Generic.PHP.DiscourageGoto.Found


declare(strict_types=1);

namespace Mwop\Contact\Listener;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;

use function sprintf;

class SendContactMessageListenerFactory
{
    public function __invoke(ContainerInterface $container): SendContactMessageListener
    {
        $config = $container->get('config-contact.message');

        if (
            ! isset($config['to'])
            || ! isset($config['sender']['address'])
        ) {
            $baseConfigKey = 'contact.message';
            throw new RuntimeException(sprintf(
                'Cannot create %s; missing required config structure.'
                . ' Requires each of: %s.to and %s.sender.address',
                SendContactMessageListener::class,
                $baseConfigKey,
                $baseConfigKey
            ));
        }

        return new SendContactMessageListener(
            mailer: $container->get('mail.transport'),
            config: $config,
            logger: $container->get(LoggerInterface::class),
        );
    }
}
