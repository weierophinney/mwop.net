<?php

declare(strict_types=1);

namespace Mwop\Blog\Mastodon;

class PostLatestEventListener
{
    public function __construct(
        private PostLatest $postLatest,
    ) {
    }

    public function __invoke(PostLatestEvent $event): void
    {
        ($this->postLatest)();
    }
}
