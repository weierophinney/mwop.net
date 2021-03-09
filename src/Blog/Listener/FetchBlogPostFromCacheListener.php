<?php // phpcs:disable Generic.WhiteSpace.ScopeIndent.IncorrectExact

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Blog\Listener;

use Mwop\Blog\BlogPost;
use Mwop\Blog\FetchBlogPostEvent;
use Psr\Cache\CacheItemPoolInterface;

use function unserialize;

class FetchBlogPostFromCacheListener
{
    public function __construct(
        private CacheItemPoolInterface $cache,
        private bool $enabled = true,
    ) {
    }

    public function __invoke(FetchBlogPostEvent $event): void
    {
        if (! $this->enabled) {
            return;
        }

        $item = $this->cache->getItem($event->id());
        if (! $item->isHit()) {
            return;
        }

        $serialized = $item->get();
        $post       = unserialize($serialized);

        if (! $post instanceof BlogPost) {
            return;
        }

        $event->provideBlogPostFromCache($post);
    }
}
