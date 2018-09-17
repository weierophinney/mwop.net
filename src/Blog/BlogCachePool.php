<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog;

use Cache\Adapter\Predis\PredisCachePool;

/**
 * PSR-6 cache pool implementation for blog posts.
 *
 * Empty extension to allow typehinting/`::class` resolution.
 */
class BlogCachePool extends PredisCachePool
{
}
