<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop;

use Psr\Cache\CacheItemPoolInterface;

return [
    'cache' => [
        'connection-parameters' => [
            'scheme' => 'tcp',
            'host' => 'redis',
            'port' => 6379,
        ],
    ],
    'zend-expressive-session-cache' => [
        'cache_item_pool_service' => SessionCachePool::class,
        'cookie_name' => 'MWOPSESS',
        'cache_limiter' => 'nocache',
        'cache_expire' => 60 * 60 * 24 * 28, // 28 days
        'persistent' => true,
    ],
];
