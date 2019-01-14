<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog;

use Psr\Cache\CacheItemPoolInterface;
use Zend\Diactoros\Response\Serializer;

class CacheListener
{
    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    public function __invoke(CacheBlogPostEvent $event) : void
    {
        $item = $event->item();
        $item->set(Serializer::toString($event->response()));
        $this->cache->save($item);
    }
}
