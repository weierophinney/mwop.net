<?php

declare(strict_types=1);

namespace Mwop\Feed;

use DateTimeImmutable;
use DateTimeInterface;
use JsonSerializable;
use Webmozart\Assert\Assert;

use function sprintf;

class FeedItem implements JsonSerializable
{
    private const REQUIRED_PAYLOAD_KEYS = [
        'title',
        'link',
        'favicon',
        'sitename',
        'siteurl',
        'created',
    ];

    public function __construct(
        public readonly string $title,
        public readonly string $link,
        public readonly string $favicon,
        public readonly string $sitename,
        public readonly string $siteurl,
        public readonly DateTimeInterface $created,
    ) {
    }

    public static function fromArray(array $payload): self
    {
        foreach (self::REQUIRED_PAYLOAD_KEYS as $key) {
            Assert::keyExists($payload, $key, sprintf('Missing "%s" in payload', $key));
            Assert::stringNotEmpty($payload[$key], sprintf('"%s" was not a non-empty-string', $key));
        }

        return new self(
            title: $payload['title'],
            link: $payload['link'],
            favicon: $payload['favicon'],
            sitename: $payload['sitename'],
            siteurl: $payload['siteurl'],
            created: new DateTimeImmutable($payload['created']),
        );
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
