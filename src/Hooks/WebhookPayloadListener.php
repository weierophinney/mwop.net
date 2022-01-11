<?php

declare(strict_types=1);

namespace Mwop\Hooks;

use Psr\Log\LoggerInterface;

class WebhookPayloadListener
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(WebhookPayload $webhook): void
    {
        $this->logger->info(sprintf(
            "Webhook payload received:\n%s\n",
            $webhook->payload
        ));
    }
}
