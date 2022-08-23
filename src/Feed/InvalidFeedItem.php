<?php

declare(strict_types=1);

namespace Mwop\Feed;

use DateTimeImmutable;
use DateTimeInterface;

class InvalidFeedItem implements FeedItem
{
    public readonly string $title;
    public readonly string $link;
    public readonly string $favicon;
    public readonly string $sitename;
    public readonly string $siteurl;
    public readonly DateTimeInterface $created;

    public function __construct()
    {
        $this->title    = '';
        $this->link     = '';
        $this->favicon  = '';
        $this->sitename = '';
        $this->siteurl  = '';
        $this->created  = new DateTimeImmutable('now');
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
