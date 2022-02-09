<?php

declare(strict_types=1);

namespace Mwop\Blog\Flickr;

use function sprintf;

class Photo
{
    use PhotoUrlTrait;

    public function __construct(
        public readonly string $id,
        public readonly string $secret,
        public readonly string $server,
        public readonly string $creditUrl,
        public readonly string $creator,
    ) {
    }

    public function __toString(): string
    {
        return sprintf(
            "%s\n[Photo by %s](%s)",
            $this->original($this->id, $this->secret, $this->server),
            $this->creator,
            $this->creditUrl,
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['originalsecret'] ?? $data['secret'],
            $data['server'],
            self::web($data['id'], $data['owner']['nsid']),
            $data['owner']['realname'],
        );
    }
}
