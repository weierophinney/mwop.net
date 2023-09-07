<?php

declare(strict_types=1);

namespace Mwop\Blog\Images;

use Stringable;

use function sprintf;
use function strtoupper;

class Image implements Stringable
{
    public function __construct(
        public readonly string $url,
        public readonly string $creator,
        public readonly string $creditUrl,
        public readonly string $title,
        public readonly string $license,
        public readonly string $licenseUrl,
    ) {
    }

    public function __toString(): string
    {
        return sprintf(
            "![%s](%s)\n> [Photo by %s](%s), licensed under [%s](%s)",
            $this->title,
            $this->url,
            $this->creator,
            $this->creditUrl,
            strtoupper($this->license),
            $this->licenseUrl,
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['url'],
            $data['creator'],
            $data['foreign_landing_url'],
            $data['title'],
            $data['license'],
            $data['license_url'],
        );
    }

    public static function fromBlogYaml(array $data): self
    {
        return new self(
            $data['url'],
            $data['creator'],
            $data['attribution_url'],
            $data['alt_text'],
            $data['license'],
            $data['license_url'],
        );
    }
}
