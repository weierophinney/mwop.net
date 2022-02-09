<?php

declare(strict_types=1);

namespace Mwop\Blog\Flickr;

use function sprintf;

class PhotoSearchResult
{
    use PhotoUrlTrait;

    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly string $secret,
        public readonly string $server,
    ) {
    }

    public function __toString(): string
    {
        return sprintf(
            '- %s [ id: %s , secret: %s ]: %s',
            $this->title,
            $this->id,
            $this->secret,
            $this->thumbnail($this->id, $this->secret, $this->server),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['title'],
            $data['secret'],
            $data['server'],
        );
    }
}
