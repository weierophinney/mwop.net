<?php

declare(strict_types=1);

namespace Mwop\Github\Webhook;

use JsonException;
use Mwop\App\HomePageCacheExpiration;
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
        private HomePageCacheExpiration $expireHomePageCache,
    ) {
    }

    public function __invoke(Payload $payload): void
    {
        $entry = AtomEntry::fromArray($this->parsePayloadJson($payload));
        if (null === $entry) {
            $this->logger->warning(sprintf(
                'Empty GitHub atom payload detected: %s',
                $payload->json
            ));
            return;
        }

        $this->logger->info(sprintf('Adding GitHub atom entry "%s" (%s)', $entry->title, $entry->link));
        $items = $this->itemList->read();
        $items->prepend($entry);
        $this->itemList->write($items);

        ($this->expireHomePageCache)();
    }

    private function parsePayloadJson(Payload $payload): array
    {
        try {
            return json_decode($payload->json, true, flags: JSON_THROW_ON_ERROR);
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
