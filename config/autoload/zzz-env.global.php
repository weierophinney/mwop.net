<?php

/**
 * Defines env-specific settings.
 */

declare(strict_types=1);

use Laminas\Stratigility\Middleware\ErrorHandler;
use Mwop\App\Factory\AccessLoggerFactory;
use Mwop\App\LoggingErrorListenerDelegator;
use Psr\Log\LoggerInterface;

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
    'cache'          => [
        'connection-parameters' => [
            'host' => 'redis',
        ],
    ],
    'dependencies'   => [
        'delegators' => [
            ErrorHandler::class          => [
                LoggingErrorListenerDelegator::class,
            ],
        ],
        'factories'  => [
            LoggerInterface::class => AccessLoggerFactory::class,
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
];
