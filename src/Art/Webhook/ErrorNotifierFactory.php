<?php

declare(strict_types=1);

namespace Mwop\Art\Webhook;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use SendGrid;

class ErrorNotifierFactory
{
    public function __invoke(ContainerInterface $container): ErrorNotifier
    {
        $config = $container->get('config-art.error_notification');

        return new ErrorNotifier(
            mailer: $container->get(SendGrid::class),
            logger: $container->get(LoggerInterface::class),
            sender: $config['sender'],
            recipient: $config['recipient'],
        );
    }
}
