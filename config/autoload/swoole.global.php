<?php
use Mwop\Factory\AccessLogFactory;
use Zend\Expressive\Swoole\Log\AccessLogInterface;
use Zend\Expressive\Swoole\StaticResourceHandler\ContentTypeFilterMiddleware;

return [
    'zend-expressive-swoole' => [
        'swoole-http-server' => [
            'host' => '0.0.0.0',
            'port' => 9000,
            'mode' => SWOOLE_PROCESS,
            'options' => [
                // This seems to be the sweet spot that prevents the server from segfaulting.
                // Test ulimit -n to see maximum the container allows, and then
                // work up towards that from 1000. 100000 gave me a lot of failures,
                // 10000 gave me none, 50000 gave me some, so... 25000.
                'max_conn' => 25000,
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
