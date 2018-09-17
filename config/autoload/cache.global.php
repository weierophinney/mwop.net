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
];
