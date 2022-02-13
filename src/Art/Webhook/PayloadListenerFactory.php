<?php

declare(strict_types=1);

namespace Mwop\Art\Webhook;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class PayloadListenerFactory
{
    public function __invoke(ContainerInterface $container): PayloadListener
    {
        return new PayloadListener(
            logger: $container->get(LoggerInterface::class),
        );
    }
}
