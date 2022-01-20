<?php

declare(strict_types=1);

namespace Mwop;

return [
    'cron' => [
        'jobs' => [
            // Fetch comics every 3 hours
            'comics' => [
                'schedule' => '0 */3 * * *',
                'event'    => Comics\ComicsEvent::class,
            ],
        ],
    ],
];
