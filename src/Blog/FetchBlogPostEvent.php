<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog;

use JsonSerializable;
use Psr\EventDispatcher\StoppableEventInterface;

class FetchBlogPostEvent implements
    JsonSerializable,
    StoppableEventInterface
{
    /** @var bool */
    private $fromCache = false;

    /** @var string */
    private $id;

    /** @var null|BlogPost */
    private $post;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function jsonSerialize() : array
    {
        return [
            'id'         => $this->id,
            'from_cache' => $this->fromCache,
            'post'       => $this->post,
        ];
    }

    public function isPropagationStopped() : bool
    {
        return $this->post && $this->fromCache;
    }

    public function blogPost() : ?BlogPost
    {
        return $this->post;
    }

    public function id() : string
    {
        return $this->id;
    }

    public function provideBlogPostFromCache(BlogPost $post) : void
    {
        $this->post = $post;
        $this->fromCache = true;
    }

    public function provideBlogPost(BlogPost $post) : void
    {
        $this->post = $post;
        $this->fromCache = false;
    }
}
