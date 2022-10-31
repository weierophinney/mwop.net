<?php

declare(strict_types=1);

namespace Mwop\Mastodon;

use Psr\Log\LoggerInterface;

class FetchMastodonFeedListener
{
    public function __construct(
        private FetchMastodonFeed $feed,
        private LoggerInterface $logger,
        private string $path = 'data/mastodon.json',
    ) {
    }

    public function __invoke(PostEvent $event): void
    {
        $entries = $this->feed->fetchEntries();
        $this->feed->cacheEntries($entries, $this->path);

        $this->logger->info('Fediverse posts written to {path}', ['path' => $this->path]);
    }
}
