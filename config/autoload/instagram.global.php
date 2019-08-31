<?php

return [
    'dependencies' => [
        'factories' => [
            \Mwop\Console\InstagramClient::class => \Mwop\Console\InstagramClientFactory::class,
            \Mwop\Console\InstagramFeed::class   => \Mwop\Console\InstagramFeedFactory::class,
        ],
    ],
    'instagram' => [
        'debug'           => false,
        'truncated_debug' => true,
        'username'        => getenv('INSTAGRAM_USERNAME'),
        'password'        => getenv('INSTAGRAM_PASSWORD'),
        'feed'            => getcwd() . '/data/instagram.feed.php',
    ],
];
