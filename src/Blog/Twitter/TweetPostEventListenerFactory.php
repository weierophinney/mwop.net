<?php

declare(strict_types=1);

namespace Mwop\Blog\Twitter;

use Psr\Container\ContainerInterface;

class TweetPostEventListenerFactory
{
    public function __invoke(ContainerInterface $container): TweetPostEventListener
    {
        return new TweetPostEventListener($container->get(TweetPost::class));
    }
}
