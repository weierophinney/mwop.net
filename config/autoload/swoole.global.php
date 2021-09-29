<?php

declare(strict_types=1);

use Mezzio\Swoole\Event\TaskEvent;
use Mezzio\Swoole\StaticResourceHandler\ContentTypeFilterMiddleware;
use Mezzio\Swoole\Task\TaskInvokerListener;

return [
    'mezzio-swoole' => [
        'enable_coroutine'   => true,
        'swoole-http-server' => [
            'process-name' => 'mwopnet',
            'host'         => '0.0.0.0',
            'port'         => 9001,
            'mode'         => SWOOLE_PROCESS,
            'options'      => [
                // For some reason, inside a docker container, ulimit -n, which is what
                // Swoole uses to determine this value by default, reports a ridiculously
                // high number. The value presented here is the value reported by the
                // docker host.
                'max_conn' => 1024,

                // Enable task workers.
                'task_worker_num' => 4,

                // PID file
                'pid_file' => sys_get_temp_dir() . '/mwop-net.pid',
            ],
            'listeners'    => [
                TaskEvent::class => [
                    TaskInvokerListener::class,
                ],
            ],
            'static-files' => [
                'type-map'   => array_merge(ContentTypeFilterMiddleware::TYPE_MAP_DEFAULT, [
                    'asc' => 'application/octet-stream',
                    'map' => 'application/octet-stream',
                ]),
                'gzip'       => [
                    'level' => 6,
                ],
                'directives' => [
                    '/\.(?:ico|png|gif|jpg|jpeg)$/' => [
                        'cache-control' => ['public', 'max-age=' . 60 * 60 * 24 * 365],
                        'last-modified' => true,
                        'etag'          => true,
                    ],
                    '/\.(?:asc)$/'                  => [
                        'cache-control' => ['public', 'max-age=' . 60 * 60 * 24 * 365],
                        'last-modified' => true,
                    ],
                    '/\.(?:css|js)$/'               => [
                        'cache-control' => ['public', 'max-age=' . 60 * 60 * 24 * 30],
                        'last-modified' => true,
                        'etag'          => true,
                    ],
                ],
            ],
        ],
    ],
];
