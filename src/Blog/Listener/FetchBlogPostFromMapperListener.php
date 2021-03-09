<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Blog\Listener;

use Mwop\Blog\BlogPost;
use Mwop\Blog\FetchBlogPostEvent;
use Mwop\Blog\Mapper\MapperInterface;

class FetchBlogPostFromMapperListener
{
    public function __construct(private MapperInterface $mapper)
    {
    }

    public function __invoke(FetchBlogPostEvent $event): void
    {
        $post = $this->mapper->fetch($event->id());

        if (! $post instanceof BlogPost) {
            return;
        }

        $event->provideBlogPost($post);
    }
}
