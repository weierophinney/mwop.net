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
$messageToAddress = $_ENV['CONTACT_MESSAGE_TO_ADDRESS'] ?? '';

/**
 * Defines env-specific settings.
 */
return [
    'blog'         => [
        'api'     => [
            'key' => $_ENV['BLOG_API_KEY'] ?? '',
        ],
        'disqus'  => [
            'key' => 'phlyboyphly',
        ],
        'cache'   => [
            'enabled' => true,
        ],
        'twitter' => [
            'consumer_key'        => $_ENV['TWITTER_CONSUMER_KEY'] ?? '',
            'consumer_secret'     => $_ENV['TWITTER_CONSUMER_SECRET'] ?? '',
            'access_token'        => $_ENV['TWITTER_ACCESS_TOKEN'] ?? '',
            'access_token_secret' => $_ENV['TWITTER_ACCESS_TOKEN_SECRET'] ?? '',
        ],
    ],
    'cache'        => [
        'connection-parameters' => [
            'host' => 'redis',
        ],
    ],
    'contact'      => [
        'recaptcha_pub_key'  => $_ENV['RECAPTCHA_PUB_KEY'] ?? '',
        'recaptcha_priv_key' => $_ENV['RECAPTCHA_PRIV_KEY'] ?? '',
        'message'            => [
            'to'     => $messageToAddress,
            'from'   => null,
            'sender' => [
                'address' => $_ENV['CONTACT_MESSAGE_SENDER_ADDRESS'] ?? '',
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
            'host'     => $_ENV['MAIL_TRANSPORT_HOST'] ?? '',
            'port'     => $_ENV['MAIL_TRANSPORT_PORT'] ?? '',
            'ssl'      => 'tls',
            'username' => $_ENV['MAIL_TRANSPORT_USERNAME'] ?? '',
            'password' => $_ENV['MAIL_TRANSPORT_PASSWORD'] ?? '',
        ],
    ],
    /*
    'oauth2'        => [
        'debug'  => [],
        'github' => [
            'clientId'     => $_ENV['OAUTH2_GITHUB_CLIENTID'],
            'clientSecret' => $_ENV['OAUTH2_GITHUB_CLIENTSECRET'],
            'redirectUri'  => $_ENV['OAUTH2_GITHUB_REDIRECTURI'],
        ],
        'google' => [
            'clientId'     => $_ENV['OAUTH2_GOOGLE_CLIENTID'],
            'clientSecret' => $_ENV['OAUTH2_GOOGLE_CLIENTSECRET'],
            'redirectUri'  => $_ENV['OAUTH2_GOOGLE_REDIRECTURI'],
            // Enable this to restrict authentication to users at the listed domain:
            // 'hostedDomain' => $_ENV['OAUTH2_GOOGLE_HOSTEDDOMAIN'],
        ],
    ],
     */
];
