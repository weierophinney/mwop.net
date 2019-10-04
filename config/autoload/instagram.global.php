<?php

return [
    'dependencies' => [
        'factories' => [
            \Mwop\Console\InstagramClient::class => \Mwop\Console\InstagramClientFactory::class,
            \Mwop\Console\InstagramFeed::class   => \Mwop\Console\InstagramFeedFactory::class,
        ],
    ],
    'instagram' => [
        'debug' => false,
        'url'   => getenv('INSTAGRAM_URL'),
        'feed'  => getcwd() . '/data/instagram.feed.php',
    ],
];
