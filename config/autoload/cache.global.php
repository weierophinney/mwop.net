<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop;

return [
    'mezzio-session-cache' => [
        'cache_item_pool_service' => App\SessionCachePool::class,
        'cookie_name'             => 'MWOPSESS',
        'cache_limiter'           => 'nocache',
        'cache_expire'            => 60 * 60 * 24 * 28, // 28 days
        'persistent'              => true,
    ],
];
