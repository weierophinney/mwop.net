<?php

declare(strict_types=1);

namespace Mwop\Blog\Images;

use function sprintf;

class Image
{
    public function __construct(
        public readonly string $url,
        public readonly string $creator,
        public readonly string $creditUrl,
        public readonly string $title,
    ) {
    }

    public function __toString(): string
    {
        return sprintf(
            "![%s](%s)\n> [Photo by %s](%s)",
            $this->title,
            $this->url,
            $this->creator,
            $this->creditUrl,
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['url'],
            $data['creator'],
            $data['foreign_landing_url'],
            $data['title'],
        );
    }

    public static function fromBlogYaml(array $data): self
    {
        return new self(
            $data['url'],
            $data['creator'],
            $data['attribution_url'],
            $data['alt_text'],
        );
    }
}
