<?php

declare(strict_types=1);

namespace Mwop\Console\Mastodon;

use DateTimeInterface;
use JsonSerializable;

class Entry implements JsonSerializable
{
    public function __construct(
        /** @var non-empty-string */
        public readonly string $link,
        /** @var non-empty-string */
        public readonly string $content,
        public DateTimeInterface $created,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'created' => $this->created->format('c'),
            'link'    => $this->link,
            'content' => $this->content,
        ];
    }
}
