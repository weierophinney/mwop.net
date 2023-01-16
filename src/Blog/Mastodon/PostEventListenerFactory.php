<?php

declare(strict_types=1);

namespace Mwop\Blog\Mastodon;

use Psr\Container\ContainerInterface;

class PostEventListenerFactory
{
    public function __invoke(ContainerInterface $container): PostEventListener
    {
        return new PostEventListener($container->get(Post::class));
    }
}
