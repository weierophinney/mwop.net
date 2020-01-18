<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Blog\Listener;

use Mwop\Blog\FetchBlogPostEvent;
use Psr\Cache\CacheItemPoolInterface;

use function serialize;

class CacheBlogPostListener
{
    /** @var CacheItemPoolInterface */
    private $cache;

    /** @var bool */
    private $enabled;

    public function __construct(
        CacheItemPoolInterface $cache,
        bool $enabled = true
    ) {
        $this->cache   = $cache;
        $this->enabled = $enabled;
    }

    public function __invoke(FetchBlogPostEvent $event): void
    {
        if (! $this->enabled) {
            return;
        }

        $post = $event->blogPost();
        if (! $post) {
            return;
        }

        $item = $this->cache->getItem($event->id());
        $item->set(serialize($post));
        $this->cache->save($item);
    }
}
