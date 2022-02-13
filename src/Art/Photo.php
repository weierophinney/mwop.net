<?php

declare(strict_types=1);

namespace Mwop\Art;

use JsonSerializable;

class Photo implements JsonSerializable
{
    public function __construct(
        public readonly string $url,
        public readonly string $sourceUrl,
        public readonly string $description,
        public readonly string $createdAt,
    ) {
    }

    public static function fromArray(array $payload): self
    {
        return new self(
            url: $payload['url'] ?? '',
            sourceUrl: $payload['source_url'] ?? '',
            description: $payload['description'] ?? '',
            createdAt: $payload['created_at'] ?? '',
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'url'         => $this->url,
            'source_url'  => $this->sourceUrl,
            'description' => $this->description,
            'created_at'  => $this->createdAt,
        ];
    }
}
