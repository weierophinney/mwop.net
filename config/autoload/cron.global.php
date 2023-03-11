<?php

declare(strict_types=1);

namespace Mwop;

use function json_encode;

return [
    'cron' => [
        'jobs' => [
            // Fetch comics every 3 hours
            'comics' => [
                'schedule' => '0 */3 * * *',
                'task'     => json_encode([
                    '__type' => Comics\ComicsEvent::class,
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            ],
            // Fetch social posts every 15 minutes
            'phpc_social' => [
                'schedule' => '*/15 * * * *',
                'task'     => json_encode([
                    '__type' => Mastodon\PostEvent::class,
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            ],
        ],
    ],
];
