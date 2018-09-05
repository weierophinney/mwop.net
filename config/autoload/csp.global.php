<?php

return [
    'content-security-policy' => [
        'default-src' => [
            'self' => true
        ],
        'frame-src' => [
            'self' => true,
            'allow' => [
                'https://platform.twitter.com',
                'https://syndication.twitter.com',
                'https://www.google.com',
                'disqus.com',
            ],
        ],
        'connect-src' => [
            'self' => true,
            'types' => [
                'https:',
            ],
        ],
        'font-src' => [
            'self' => true,
            'types' => [
                'chrome-extension:',
                'https:',
            ],
        ],
        'img-src' => [
            'self' => true,
            'types' => [
                'data:',
                'http:',
                'https:',
            ],
        ],
        'script-src' => [
            'self' => true,
            'types' => [
                'data:',
            ],
            'allow' => [
                'https://cdn.ampproject.org',
                'https://www.google.com',
                'https://www.google-analytics.com',
                'https://www.gstatic.com',
                'https://code.jquery.com',
                '*.disqus.com',
                '*.disquscdn.com',
                'https://platform.twitter.com',
                'https://*.twimg.com',
            ],
        ],
        'style-src' => [
            'self' => true,
            'unsafe-inline' => true, // allow inlined styles; mostly for widgets
            'allow' => [
                'https://fonts.googleapis.com',
                'platform.twitter.com',
                'https://*.twimg.com',
                '*.disqus.com',
                '*.disquscdn.com',
            ],
        ],
    ],
];
