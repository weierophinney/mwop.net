<?php

declare(strict_types=1);

namespace Mwop\Mastodon;

use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\Mapper\TreeMapper;
use Laminas\Feed\Reader\Entry\EntryInterface;
use Laminas\Feed\Reader\Reader;

class FetchMastodonFeed
{
    private const FEED_URI = 'https://phpc.social/users/mwop.rss';

    public function __construct(
        private TreeMapper $mapper,
    ) {
    }

    public function __invoke(): void
    {
        
    }

    public function fetchEntries(): Collection
    {
        $feed = Reader::import(self::FEED_URI);

        return Collection::make($feed)
            ->map(fn (EntryInterface $entry): Entry => $this->mapper->map(Entry::class, Source::array([
                'link'    => $entry->getLink(),
                'content' => $entry->getDescription(),
                'created' => $entry->getDateCreated(),
            ])));
    }

    public function cacheEntries(Collection $entries, string $path): void
    {
        file_put_contents($path, $entries->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
}
