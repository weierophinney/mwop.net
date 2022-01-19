<?php

declare(strict_types=1);

namespace Mwop\Feed;

use DateTimeImmutable;
use DateTimeInterface;
use JsonSerializable;
use Webmozart\Assert\Assert;

use function array_key_exists;
use function preg_match;
use function sprintf;

class FeedItem implements JsonSerializable
{
    private const REQUIRED_PAYLOAD_KEYS = [
        'title',
        'link',
        'sitename',
        'siteurl',
        'created',
    ];

    private const DEFAULT_FAVICON_MAP = [
        'https://www.zend.com/' => 'https://www.zend.com/sites/zend/themes/custom/zend/images/favicons/favicon.ico',
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

    public static function fromArray(array $payload): ?self
    {
        if (
            array_key_exists('author', $payload)
            && ! preg_match('/phinney/i', $payload['author'])
        ) {
            return null;
        }

        foreach (self::REQUIRED_PAYLOAD_KEYS as $key) {
            Assert::keyExists($payload, $key, sprintf('Missing "%s" in payload', $key));
            Assert::stringNotEmpty($payload[$key], sprintf('"%s" was not a non-empty-string', $key));
        }

        if (array_key_exists('favicon', $payload)) {
            Assert::stringNotEmpty($payload['favicon'], '"favicon" was not a non-empty-string');
        }

        if (! 
            array_key_exists('favicon', $payload)
            && array_key_exists($payload['siteurl'], self::DEFAULT_FAVICON_MAP)
        ) {
            $payload['favicon'] = self::DEFAULT_FAVICON_MAP[$payload['siteurl']];
        }

        return new self(
            title: $payload['title'],
            link: $payload['link'],
            favicon: $payload['favicon'] ?? '',
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
