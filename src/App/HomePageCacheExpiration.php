<?php

declare(strict_types=1);

namespace Mwop\App;

use Psr\Cache\CacheItemPoolInterface;
use Throwable;

use function hash;
use function substr;

class HomePageCacheExpiration
{
    public function __construct(
        private CacheItemPoolInterface $cache,
    ) {
    }

    public function __invoke(): void
    {
        $key = substr(hash('sha256', '/'), 0, 16);
        if (! $this->cache->hasItem($key)) {
            return;
        }

        try {
            $this->cache->deleteItem($key);
        } catch (Throwable) {
            // Don't really care if we have an issue here
        }
    }
}
