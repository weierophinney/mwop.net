<?php

declare(strict_types=1);

return [
    'dependencies' => [
        'factories' => [
            Mwop\Console\InstagramClient::class => Mwop\Console\InstagramClientFactory::class,
            Mwop\Console\InstagramFeed::class   => Mwop\Console\InstagramFeedFactory::class,
        ],
    ],
    'instagram'    => [
        'debug'      => false,
        'login'      => $_ENV['INSTAGRAM_LOGIN'],
        'password'   => $_ENV['INSTAGRAM_PASSWORD'],
        'profile'    => $_ENV['INSTAGRAM_PROFILE'],
        'cache_path' => getcwd() . '/data/cache/instagram/',
        'feed'       => getcwd() . '/data/instagram.feed.php',
    ],
];
