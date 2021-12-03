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
    'oauth2'               => [
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
    'mezzio'               => [
        'error_handler' => [
            'template_404'   => 'error::404',
            'template_error' => 'error::500',
        ],
    ],
];
