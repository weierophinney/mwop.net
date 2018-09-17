<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

return [
    'session' => [
        'cache' => [
            'connection-parameters' => [
                'scheme' => 'tcp',
                'host' => 'redis',
                'port' => 6379,
            ],
            'client-options' => [
                'prefix' => 'session:',
            ],
        ],
    ],
];
