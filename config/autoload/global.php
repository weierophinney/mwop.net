<?php

declare(strict_types=1);

return [
    'blog'                 => [
        'db'    => 'sqlite:' . realpath(getcwd()) . '/data/posts.db',
        'cache' => [
            'enabled' => true,
        ],
    ],
    'config_cache_enabled' => false,
    'debug'                => false,
    'github'               => [
        'user' => 'weierophinney',
    ],
    'mezzio'               => [
        'error_handler' => [
            'template_404'   => 'error::404',
            'template_error' => 'error::500',
        ],
    ],
];
