<?php

declare(strict_types=1);

namespace Mwop\Art;

use DateTimeInterface;
use JsonSerializable;

class Photo implements JsonSerializable
{
    public function __construct(
        public readonly string $url,
        public readonly string $sourceUrl,
        public readonly string $description,
        public readonly DateTimeInterface $createdAt,
        private ?string $filename = null,
    ) {
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
}
