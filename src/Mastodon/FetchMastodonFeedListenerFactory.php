<?php

declare(strict_types=1);

namespace Mwop\Mastodon;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class FetchMastodonFeedListenerFactory
{
    public function __invoke(ContainerInterface $container): FetchMastodonFeedListener
    {
        return new FetchMastodonFeedListener(
            feed: $container->get(FetchMastodonFeed::class),
            logger: $container->get(LoggerInterface::class),
        );
    }
}
