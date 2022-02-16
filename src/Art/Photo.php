<?php

declare(strict_types=1);

namespace Mwop\Art;

use DateTimeImmutable;
use DateTimeInterface;
use JsonSerializable;

class Photo implements JsonSerializable
{
    public readonly DateTimeInterface $createdAt;

    public function __construct(
        public readonly string $url,
        public readonly string $sourceUrl,
        public readonly string $description,
        string|DateTimeInterface $createdAt,
        private ?string $filename = null,
    ) {
        if (is_string($createdAt)) {
            $createdAt = $this->transformStringDateTime();
        }
        $this->createdAt = $createdAt;
    }

    public static function fromArray(array $payload): self
    {
        return new self(
            url: $payload['url'] ?? '',
            sourceUrl: $payload['source_url'] ?? '',
            description: $payload['description'] ?? '',
            createdAt: $payload['created_at'] ?? '',
            filename: $payload['filename'] ?? null,
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'filename'    => $this->filename,
            'url'         => $this->url,
            'source_url'  => $this->sourceUrl,
            'description' => $this->description,
            'created_at'  => $this->createdAt->format(DateTimeInterface::ISO8601),
        ];
    }

    public function filename(): ?string
    {
        return $this->filename;
    }

    public function injectFilename(string $filename): void
    {
        $this->filename = $filename;
    }

    private function transformStringDateTime(string $dateTime): DateTimeInterface
    {
        $matches = [];

        if (! preg_match('/^(?P<month>\S+) (?P<day>\d+), (?P<year>\d{4}) at $(?P<time>\d{2}:\d{2})(?P<>meridian>am|pm)$/i', $dateTime, $matches)) {
            return new DateTimeImmutable($dateTime);
        }

        return new DateTimeImmutable(sprintf(
            '%s %d, %d %s%s',
            $matches['month'],
            $matches['day'],
            $matches['year'],
            $matches['time'],
            $matches['meridian'],
        ));
    }
}
