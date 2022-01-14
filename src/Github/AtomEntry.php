<?php

declare(strict_types=1);

namespace Mwop\Github;

use JsonSerializable;
use Stringable;
use Webmozart\Assert\Assert;

use function json_decode;
use function preg_match;
use function sprintf;

use const JSON_THROW_ON_ERROR;

class AtomEntry implements JsonSerializable, Stringable
{
    private const TEMPLATE = '<li><a href="%s">%s</a></li>';

    public function __construct(
        public readonly string $link,
        public readonly string $title,
        public readonly string $content,
    ) {
    }

    public static function fromWebhookPayload(string $payload): ?self
    {
        $feedItem = json_decode($webhook->payload, true, 3, JSON_THROW_ON_ERROR);
        return self::fromArray($feedItem);
    }

    public static function fromArray(array $payload): ?self
    {
        Assert::keyExists($payload, 'link', 'Missing "link" in payload');
        Assert::stringNotEmpty($payload['link'], 'Link was not a non-empty-string');

        if (preg_match('#weierophinney/.*?mwop\.net#', $payload['link'])) {
            // Ignore items related to my websites
            return null;
        }

        Assert::keyExists($payload, 'title', 'Missing "title" in payload');
        Assert::stringNotEmpty($payload['title'], 'Title was not a non-empty-string');
        Assert::keyExists($payload, 'content', 'Missing "content" in payload');
        Assert::stringNotEmpty($payload['content'], 'Content was not a non-empty-string');

        return new self(
            $payload['link'],
            $payload['title'],
            $payload['content'],
        );
    }

    public function __toString(): string
    {
        return sprintf(self::TEMPLATE, $this->link, $this->title);
    }

    public function jsonSerialize(): array
    {
        return [
            'link'    => $this->link,
            'title'   => $this->title,
            'content' => $this->content,
        ];
    }
}
