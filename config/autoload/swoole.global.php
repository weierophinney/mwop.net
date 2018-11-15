<?php
use Mwop\Factory\AccessLogFactory;
use Zend\Expressive\Swoole\Log\AccessLogInterface;
use Zend\Expressive\Swoole\StaticResourceHandler\ContentTypeFilterMiddleware;

return [
    'zend-expressive-swoole' => [
        'enable_coroutine' => true,
        'swoole-http-server' => [
            'host' => '0.0.0.0',
            'port' => 9000,
            'mode' => SWOOLE_PROCESS,
            'options' => [
                // For some reason, inside a docker container, ulimit -n, which is what
                // Swoole uses to determine this value by default, reports a ridiculously
                // high number. The value presented here is the value reported by the
                // docker host.
                'max_conn' => 1024,

                // Enable task workers.
                'task_worker_num' => 4,
            ],
            'static-files' => [
                'type-map' => array_merge(ContentTypeFilterMiddleware::TYPE_MAP_DEFAULT, [
                    'asc' => 'application/octet-stream',
                ]),
                'gzip' => [
                    'level' => 6,
                ],
                'directives' => [
                    '/\.(?:ico|png|gif|jpg|jpeg)$/' => [
                        'cache-control' => ['public', 'max-age=' . 60 * 60 * 24 * 365],
                        'last-modified' => true,
                        'etag' => true,
                    ],
                    '/\.(?:asc)$/' => [
                        'cache-control' => ['public', 'max-age=' . 60 * 60 * 24 * 365],
                        'last-modified' => true,
                    ],
                    '/\.(?:css|js)$/' => [
                        'cache-control' => ['public', 'max-age=' . 60 * 60 * 24 * 30],
                        'last-modified' => true,
                        'etag' => true,
                    ],
                ],

            ],
        ],
    ],
];
