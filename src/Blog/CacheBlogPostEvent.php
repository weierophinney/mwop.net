<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog;

use Psr\Cache\CacheItemInterface;
use Psr\Http\Message\ResponseInterface as Response;

class CacheBlogPostEvent
{
    /** @var CacheItemInterface */
    private $cacheItem;

    /** @var Response */
    private $response;

    public function __construct(CacheItemInterface $cacheItem, Response $response)
    {
        $this->cacheItem = $cacheItem;
        $this->response  = $response;
    }

    public function item() : CacheItemInterface
    {
        return $this->cacheItem;
    }

    public function response() : Response
    {
        return $this->response;
    }
}
