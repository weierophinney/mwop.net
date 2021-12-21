<?php // phpcs:disable Generic.WhiteSpace.ScopeIndent.IncorrectExact


declare(strict_types=1);

namespace Mwop\Blog;

use DateTimeInterface;

class BlogPost
{
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly string $author,
        public readonly DateTimeInterface $created,
        public readonly ?DateTimeInterface $updated,
        public readonly array $tags,
        public readonly string $body,
        public readonly string $extended,
        public readonly bool $isDraft,
        public readonly bool $isPublic
    ) {
    }
}
