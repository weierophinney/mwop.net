<?php

declare(strict_types=1);

namespace Mwop\Blog\Twitter;

class TweetPostEventListener
{
    public function __construct(
        private TweetPost $tweetPost,
    ) {
    }

    public function __invoke(TweetPostEvent $event): void
    {
        ($this->tweetPost)($event->id);
    }
}
