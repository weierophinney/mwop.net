<?php

declare(strict_types=1);

namespace Mwop\Github\Webhook;

use CuyZ\Valinor\Mapper\MappingError;
use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\Mapper\TreeMapper;
use Mwop\App\HomePageCacheExpiration;
use Mwop\Github\AtomEntry;
use Mwop\Github\ItemList;
use Psr\Log\LoggerInterface;

use function sprintf;

class PayloadListener
{
    public function __construct(
        private ItemList $itemList,
        private LoggerInterface $logger,
        private HomePageCacheExpiration $expireHomePageCache,
        private TreeMapper $mapper,
    ) {
    }

    public function __invoke(Payload $payload): void
    {
        $entry = $this->parsePayloadJson($payload);
        if (null === $entry || null === $entry->link) {
            return;
        }

        $this->logger->info(sprintf('Adding GitHub atom entry "%s" (%s)', $entry->title, $entry->link));
        $items = $this->itemList->read();
        $items->prepend($entry);
        $this->itemList->write($items);

        ($this->expireHomePageCache)();
    }

    private function parsePayloadJson(Payload $payload): ?AtomEntry
    {
        try {
            return $this->mapper->map(AtomEntry::class, Source::json($payload->payload));
        } catch (MappingError $e) {
            $this->logger->warning(sprintf(
                "Unable to parse GitHub atom entry webhook payload: %s\nPayload: %s",
                $e->getMessage(),
                $payload->payload
            ));
            return null;
        }
    }
}
