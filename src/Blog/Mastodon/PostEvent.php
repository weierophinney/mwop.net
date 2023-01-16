<?php

declare(strict_types=1);

namespace Mwop\Blog\Mastodon;

class PostEvent
{
    public function __construct(
        private readonly string $id,
    ) {
    }
}
