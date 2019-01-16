<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog\Listener;

use Mwop\Blog\BlogPost;
use Mwop\Blog\BlogPostEvent;
use Mwop\Blog\Mapper\MapperInterface;

class FetchBlogPostFromMapperListener
{
    /**
     * @var MapperInterface
     */
    private $mapper;

    public function __construct(MapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }

    public function __invoke(BlogPostEvent $event) : void
    {
        $post = $this->mapper->fetch($event->id());

        if (! $post instanceof BlogPost) {
            return;
        }

        $event->provideBlogPost($post);
    }
}
