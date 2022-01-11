<?php

declare(strict_types=1);

use Laminas\Stratigility\Middleware\ErrorHandler;
use Mezzio\Swoole\Task\DeferredServiceListenerDelegator;
use Mwop\App\Factory\AccessLoggerFactory;
use Mwop\App\LoggingErrorListenerDelegator;
use Mwop\Blog\Listener\CacheBlogPostListener;
use Mwop\Contact\Listener\SendContactMessageListener;
use Psr\Log\LoggerInterface;

/** @var string $messageToAddress */
$messageToAddress = $_SERVER['CONTACT_MESSAGE_TO_ADDRESS'] ?? '';

/** @var string $messageToName */
$messageToName = $_SERVER['CONTACT_MESSAGE_TO_FULLNAME'] ?? '';

/** @var string $messageFromAddress */
$messageFromAddress = $_SERVER['CONTACT_MESSAGE_SENDER_ADDRESS'] ?? '';

/**
 * Defines env-specific settings.
 */
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
    'contact'        => [
        'recaptcha_pub_key'  => $_SERVER['RECAPTCHA_PUB_KEY'] ?? '',
        'recaptcha_priv_key' => $_SERVER['RECAPTCHA_PRIV_KEY'] ?? '',
        'message'            => [
            'to'     => $messageToAddress,
            'from'   => null,
            'sender' => [
                'address' => $messageFromAddress,
                'name'    => 'mwop.net Contact Form',
            ],
        ],
    ],
    'dependencies'   => [
        'delegators' => [
            CacheBlogPostListener::class      => [
                DeferredServiceListenerDelegator::class,
            ],
            ErrorHandler::class               => [
                LoggingErrorListenerDelegator::class,
            ],
            SendContactMessageListener::class => [
                DeferredServiceListenerDelegator::class,
            ],
        ],
        'factories'  => [
            LoggerInterface::class => AccessLoggerFactory::class,
        ],
    ],
    'hooks' => [
        'token-value'  => $_SERVER['WEBHOOK_TOKEN'] ?? '',
    ],
    'mail'           => [
        'transport' => [
            'apikey' => $_SERVER['SENDGRID_APIKEY'] ?? '',
        ],
    ],
];
