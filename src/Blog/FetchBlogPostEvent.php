<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Blog;

use JsonSerializable;
use Psr\EventDispatcher\StoppableEventInterface;

class FetchBlogPostEvent implements
    JsonSerializable,
    StoppableEventInterface
{
    private bool $fromCache = false;

    private ?BlogPost $post = null;

    public function __construct(private string $id)
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'id'         => $this->id,
            'from_cache' => $this->fromCache,
            'post'       => $this->post,
        ];
    }

    public function isPropagationStopped(): bool
    {
        return $this->post && $this->fromCache;
    }

    public function blogPost(): ?BlogPost
    {
        return $this->post;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function provideBlogPostFromCache(BlogPost $post): void
    {
        $this->post      = $post;
        $this->fromCache = true;
    }

    public function provideBlogPost(BlogPost $post): void
    {
        $this->post      = $post;
        $this->fromCache = false;
    }
}
