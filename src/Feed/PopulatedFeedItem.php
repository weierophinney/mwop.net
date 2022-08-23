<?php

declare(strict_types=1);

namespace Mwop\Feed;

use DateTimeInterface;

class PopulatedFeedItem implements FeedItem
{
    public function __construct(
        /** @var non-empty-string */
        public readonly string $title,
        /** @var non-empty-string */
        public readonly string $link,
        public readonly string $favicon,
        /** @var non-empty-string */
        public readonly string $sitename,
        /** @var non-empty-string */
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
