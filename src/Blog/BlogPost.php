<?php // phpcs:disable Generic.WhiteSpace.ScopeIndent.IncorrectExact


declare(strict_types=1);

namespace Mwop\Blog;

use DateTimeInterface;

class BlogPost
{
    public function __construct(
        public string $id,
        public string $title,
        public string $author,
        public DateTimeInterface $created,
        public ?DateTimeInterface $updated,
        public array $tags,
        public string $body,
        public string $extended,
        public bool $isDraft,
        public bool $isPublic
    ) {
    }
}
