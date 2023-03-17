<?php

declare(strict_types=1);

return [
    'redis-task-queue' => [
        'signals' => [
            SIGINT,
            SIGTERM,
        ],
    ],
];
