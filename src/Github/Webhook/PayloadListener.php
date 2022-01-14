<?php

declare(strict_types=1);

namespace Mwop\Github\Webhook;

use JsonException;
use Mwop\Github\AtomEntry;
use Mwop\Github\ItemList;
use Psr\Log\LoggerInterface;

use function json_decode;
use function sprintf;

use const JSON_THROW_ON_ERROR;

class PayloadListener
{
    public function __construct(
        private ItemList $itemList,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(Payload $payload): void
    {
        $entry = AtomEntry::fromArray($this->parsePayloadJson($payload));
        if (null === $entry) {
            return;
        }

        $items = $this->itemList->read();
        $items->prepend($entry);
        $this->itemList->write($items);
    }

    private function parsePayloadJson(Payload $payload): array
    {
        try {
            return json_decode($payload->json, true, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->logger->warning(sprintf(
                "Unable to parse GitHub atom entry webhook payload: %s\nPayload: %s",
                $e->getMessage(),
                $payload->json
            ));
            return [];
        }
    }
}
