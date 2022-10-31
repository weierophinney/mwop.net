<?php

declare(strict_types=1);

namespace Mwop\Mastodon\Console;

use Mwop\Mastodon\FetchMastodonFeed as FetchMastodonFeedService;
use Psr\Container\ContainerInterface;

class FetchMastodonFeedFactory
{
    public function __invoke(ContainerInterface $container): FetchMastodonFeed
    {
        return new FetchMastodonFeed(
            feed: $container->get(FetchMastodonFeedService::class),
        );
    }
}
