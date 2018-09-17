<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog;

return [
    'blog' => [
        'cache' => [
            'connection-parameters' => [
                'scheme' => 'tcp',
                'host' => 'redis',
                'port' => 6379,
            ],
            'client-options' => [
                'prefix' => 'blog:',
            ],
        ],
    ],
    'dependencies' => [
        'factories' => [
            BlogCachePool::class => BlogCachePoolFactory::class,
        ],
    ],
];
