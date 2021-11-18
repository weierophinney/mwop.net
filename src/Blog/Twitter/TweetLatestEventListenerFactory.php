<?php

declare(strict_types=1);

namespace Mwop\Blog\Twitter;

use Psr\Container\ContainerInterface;

class TweetLatestEventListenerFactory
{
    public function __invoke(ContainerInterface $container): TweetLatestEventListener
    {
        return new TweetLatestEventListener($container->get(TweetLatest::class));
    }
}
