<?php

declare(strict_types=1);

namespace Mwop\Blog\Mastodon;

class PostEventListener
{
    public function __construct(
        private Post $post,
    ) {
    }

    public function __invoke(PostEvent $event): void
    {
        ($this->post)($event->id);
    }
}
