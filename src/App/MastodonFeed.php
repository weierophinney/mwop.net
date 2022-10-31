<?php

declare(strict_types=1);

namespace Mwop\App;

use CuyZ\Valinor\Mapper\MappingError;
use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\Mapper\TreeMapper;
use JsonException;
use Mwop\Console\Mastodon\Collection;
use Mwop\Console\Mastodon\Entry;
use Psr\Log\LoggerInterface;

use function file_get_contents;
use function json_decode;

use const JSON_THROW_ON_ERROR;

class MastodonFeed
{
    public function __construct(
        /** @psalm-var non-empty-string */
        private readonly string $feedPath,
        private TreeMapper $mapper,
        private LoggerInterface $logger,
    ) {
    }

    public function read(): ?Collection
    {
        $json = file_get_contents($this->feedPath);

        try {
            $raw = json_decode($json, associative: true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->logger->warning('Error parsing Mastodon social feed: {error}', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }

        $items = Collection::make($raw);

        try {
            $items = $items->map(fn (array $entry): Entry => $this->mapper->map(Entry::class, Source::array($entry)));
        } catch (MappingError $e) {
            $this->logger->warning('Error mapping Mastodon social feed item: {error}', [
                'error' => $e->getMessage(),
            ]);

            while (null !== ($e = $e->getPrevious())) {
                $this->logger->warning('Previous message: {error}', [
                    'error' => $e->getMessage(),
                ]);
            }

            return null;
        }

        return $items;
    }
}
