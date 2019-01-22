<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

return [
    'blog' => [
        'db'     => 'sqlite:' . realpath(getcwd()) . '/data/posts.db',
        'cache'  => [
            'enabled' => true,
        ],
    ],
    'config_cache_enabled' => false,
    'debug' => false,
    'github' => [
        'user' => 'weierophinney',
    ],
    'oauth2' => [
        'github' => [
            'clientId'     => null,
            'clientSecret' => null,
            'redirectUri'  => null,
        ],
        'google' => [
            'clientId'     => null,
            'clientSecret' => null,
            'redirectUri'  => null,
            // Enable this to restrict authentication to users at the listed domain:
            // 'hostedDomain' => 'https://mwop.net',
        ],
    ],
    'zend-expressive' => [
        'error_handler' => [
            'template_404'   => 'error::404',
            'template_error' => 'error::500',
        ],
    ],
];
