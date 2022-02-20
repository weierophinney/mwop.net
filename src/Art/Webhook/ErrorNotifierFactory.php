<?php

declare(strict_types=1);

namespace Mwop\Art\Webhook;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class ErrorNotifierFactory
{
    public function __invoke(ContainerInterface $container): ErrorNotifier
    {
        $config = $container->get('config-art');

        return new ErrorNotifier(
            mailer: $container->get('mail.transport'),
            logger: $container->get(LoggerInterface::class),
            sender: $config['error_notification']['sender'],
            recipient: $config['error_notification']['recipient'],
        );
    }
}
