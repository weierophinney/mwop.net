<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\App;

use Cache\Hierarchy\HierarchicalPoolInterface;
use Cache\Namespaced\NamespacedCachePool;

/**
 * PSR-6 cache pool implementation for sessions.
 *
 * Empty extension to allow typehinting/`::class` resolution.
 */
class SessionCachePool extends NamespacedCachePool
{
    public function __construct(HierarchicalPoolInterface $cachePool)
    {
        parent::__construct($cachePool, 'session');
    }
}
