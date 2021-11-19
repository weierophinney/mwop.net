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

/**
 * Defines env-specific settings.
 */
return [
    'blog'         => [
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
    'cache'        => [
        'connection-parameters' => [
            'host' => 'redis',
        ],
    ],
    'contact'      => [
        'recaptcha_pub_key'  => $_SERVER['RECAPTCHA_PUB_KEY'] ?? '',
        'recaptcha_priv_key' => $_SERVER['RECAPTCHA_PRIV_KEY'] ?? '',
        'message'            => [
            'to'     => $messageToAddress,
            'from'   => null,
            'sender' => [
                'address' => $_SERVER['CONTACT_MESSAGE_SENDER_ADDRESS'] ?? '',
            ],
        ],
    ],
    'dependencies' => [
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
    'mail'         => [
        'transport' => [
            'class'    => Swift_SmtpTransport::class,
            'host'     => $_SERVER['MAIL_TRANSPORT_HOST'] ?? '',
            'port'     => $_SERVER['MAIL_TRANSPORT_PORT'] ?? '',
            'ssl'      => 'tls',
            'username' => $_SERVER['MAIL_TRANSPORT_USERNAME'] ?? '',
            'password' => $_SERVER['MAIL_TRANSPORT_PASSWORD'] ?? '',
        ],
    ],
    /*
    'oauth2'        => [
        'debug'  => [],
        'github' => [
            'clientId'     => $_SERVER['OAUTH2_GITHUB_CLIENTID'],
            'clientSecret' => $_SERVER['OAUTH2_GITHUB_CLIENTSECRET'],
            'redirectUri'  => $_SERVER['OAUTH2_GITHUB_REDIRECTURI'],
        ],
        'google' => [
            'clientId'     => $_SERVER['OAUTH2_GOOGLE_CLIENTID'],
            'clientSecret' => $_SERVER['OAUTH2_GOOGLE_CLIENTSECRET'],
            'redirectUri'  => $_SERVER['OAUTH2_GOOGLE_REDIRECTURI'],
            // Enable this to restrict authentication to users at the listed domain:
            // 'hostedDomain' => $_SERVER['OAUTH2_GOOGLE_HOSTEDDOMAIN'],
        ],
    ],
     */
];
