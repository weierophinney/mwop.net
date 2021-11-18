<?php

declare(strict_types=1);

namespace Mwop\Blog\Twitter;

class TweetLatestEventListener
{
    public function __construct(
        private TweetLatest $tweetLatest,
    ) {
    }

    public function __invoke(TweetLatestEvent $event): void
    {
        ($this->tweetLatest)();
    }
}
