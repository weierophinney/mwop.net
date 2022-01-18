<?php

declare(strict_types=1);

namespace Mwop\Console;

use DateTimeInterface;
use JsonSerializable;

class FeedItem implements JsonSerializable
{
    public function __construct(
        public readonly string $title,
        public readonly string $link,
        public readonly string $favicon,
        public readonly string $sitename,
        public readonly string $siteurl,
        public readonly DateTimeInterface $created,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'title'    => $this->title,
            'link'     => $this->link,
            'favicon'  => $this->favicon,
            'sitename' => $this->sitename,
            'siteurl'  => $this->siteurl,
            'created'  => $this->created->format('c'),
        ];
    }
}
