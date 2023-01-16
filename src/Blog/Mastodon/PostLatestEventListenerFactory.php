<?php

declare(strict_types=1);

namespace Mwop\Blog\Mastodon;

use Psr\Container\ContainerInterface;

class PostLatestEventListenerFactory
{
    public function __invoke(ContainerInterface $container): PostLatestEventListener
    {
        return new PostLatestEventListener($container->get(PostLatest::class));
    }
}
