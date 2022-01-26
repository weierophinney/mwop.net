<?php

declare(strict_types=1);

namespace Mwop\Blog\Twitter;

class TweetPostEvent
{
    public function __construct(
        private readonly string $id,
    ) {
    }
}
