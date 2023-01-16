<?php

/**
 * Defines env-specific settings.
 */

declare(strict_types=1);

use Laminas\Stratigility\Middleware\ErrorHandler;
use Mezzio\Swoole\Task\DeferredServiceListenerDelegator;
use Mwop\App\Factory\AccessLoggerFactory;
use Mwop\App\LoggingErrorListenerDelegator;
use Mwop\Blog\Listener\CacheBlogPostListener;
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
        'twitter' => [
            'consumer_key'        => $_SERVER['TWITTER_CONSUMER_KEY'] ?? '',
            'consumer_secret'     => $_SERVER['TWITTER_CONSUMER_SECRET'] ?? '',
            'access_token'        => $_SERVER['TWITTER_ACCESS_TOKEN'] ?? '',
            'access_token_secret' => $_SERVER['TWITTER_ACCESS_TOKEN_SECRET'] ?? '',
        ],
    ],
    'cache'          => [
        'connection-parameters' => [
            'host' => 'redis',
        ],
    ],
    'dependencies'   => [
        'delegators' => [
            CacheBlogPostListener::class => [
                DeferredServiceListenerDelegator::class,
            ],
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
    'mastodon'       => [
        'domain'        => $_SERVER['MASTO_DOMAIN'],
        'client_id'     => $_SERVER['MASTO_CLIENT_ID'],
        'client_secret' => $_SERVER['MASTO_CLIENT_SECRET'],
    ],
];
