<?php

declare(strict_types=1);

return [
    'content-security-policy' => [
        'default-src' => [
            'self' => true,
        ],
        'frame-src'   => [
            'self'  => true,
            'allow' => [
                'disqus.com',
            ],
        ],
        'connect-src' => [
            'self'  => true,
            'types' => [
                'https:',
            ],
        ],
        'font-src'    => [
            'self'  => true,
            'types' => [
                'chrome-extension:',
                'https:',
            ],
        ],
        'img-src'     => [
            'self'  => true,
            'types' => [
                'data:',
                'http:',
                'https:',
            ],
        ],
        'script-src'  => [
            'self'          => true,
            'types'         => [
                'data:',
            ],
            'allow'         => [
                '*.disqus.com',
                '*.disquscdn.com',
            ],
            'unsafe-inline' => true,
        ],
        // Not honored yet by paragonie/csp-builder:
        'prefetch-src' => [
            'self'  => true,
            'types' => [
                'data:',
                'http:',
                'https:',
            ],
            'allow' => [
                '*.disqus.com',
                '*.disquscdn.com',
            ],
        ],
        'style-src'    => [
            'self'          => true,
            'unsafe-inline' => true, // allow inlined styles; mostly for widgets
            'allow'         => [
                'https://fonts.googleapis.com',
                '*.disqus.com',
                '*.disquscdn.com',
            ],
        ],
    ],
];
