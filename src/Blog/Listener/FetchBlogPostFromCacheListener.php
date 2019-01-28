<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\Blog\Listener;

use Mwop\Blog\BlogPost;
use Mwop\Blog\FetchBlogPostEvent;
use Psr\Cache\CacheItemPoolInterface;

use function unserialize;

class FetchBlogPostFromCacheListener
{
    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var bool
     */
    private $enabled;

    public function __construct(
        CacheItemPoolInterface $cache,
        bool $enabled = true
    ) {
        $this->cache   = $cache;
        $this->enabled = $enabled;
    }

    public function __invoke(FetchBlogPostEvent $event) : void
    {
        if (! $this->enabled) {
            return;
        }

        $item = $this->cache->getItem($event->id());
        if (! $item->isHit()) {
            return;
        }

        $serialized = $item->get();
        $post = unserialize($serialized);

        if (! $post instanceof BlogPost) {
            return;
        }

        $event->provideBlogPostFromCache($post);
    }
}
