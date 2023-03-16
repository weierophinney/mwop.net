<?php

/**
 * Defines env-specific settings.
 */

declare(strict_types=1);

use Laminas\Stratigility\Middleware\ErrorHandler;
use Mwop\App\LoggingErrorListenerDelegator;

return [
    'authentication' => [
        'allowed_credentials' => [
            'username' => $_SERVER['AUTH_USERNAME'] ?? null,
            'password' => $_SERVER['AUTH_PASSWORD'] ?? null,
        ],
    ],
    'blog'           => [
        'api'     => [
            'key' => $_SERVER['BLOG_API_KEY'] ?? '',
        ],
        'disqus'  => [
            'key' => 'phlyboyphly',
        ],
        'cache'   => [
            'enabled' => true,
        ],
    ],
    'dependencies'   => [
        'delegators' => [
            ErrorHandler::class          => [
                LoggingErrorListenerDelegator::class,
            ],
        ],
    ],
    'hooks'          => [
        'token-value' => $_SERVER['WEBHOOK_TOKEN'] ?? '',
    ],
    'mail'           => [
        'transport' => [
            'apikey' => $_SERVER['SENDGRID_APIKEY'] ?? '',
        ],
    ],
    'redis'          => [
        'connection-parameters' => [
            'host' => 'redis',
        ],
    ],
];
