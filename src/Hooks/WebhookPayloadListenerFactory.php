<?php

declare(strict_types=1);

namespace Mwop\Hooks;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class WebhookPayloadListenerFactory
{
    public function __invoke(ContainerInterface $container): WebhookPayloadListener
    {
        return new WebhookPayloadListener(
            $container->get(LoggerInterface::class)
        );
    }
}
