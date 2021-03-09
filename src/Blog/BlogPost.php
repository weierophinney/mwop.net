<?php // phpcs:disable Generic.WhiteSpace.ScopeIndent.IncorrectExact

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

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
