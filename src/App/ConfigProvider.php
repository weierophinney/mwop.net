<?php

declare(strict_types=1);

namespace Mwop\App;

use Aws\S3\S3Client;
use CuyZ\Valinor\MapperBuilder;
use Laminas\Feed\Reader\Http\ClientInterface as FeedReaderHttpClientInterface;
use Laminas\ServiceManager\Factory\InvokableFactory;
use League\Plates\Engine;
use Mezzio\Application;
use Mezzio\Authentication\AuthenticationInterface;
use Mezzio\Authentication\AuthenticationMiddleware;
use Mezzio\Authentication\UserRepositoryInterface;
use Mezzio\Authorization\AuthorizationInterface;
use Mezzio\Authorization\AuthorizationMiddleware;
use Mezzio\Authorization\Rbac\LaminasRbac;
use Mezzio\Session\SessionMiddleware;
use Middlewares\Csp;
use Mwop\App\Factory\UserRepositoryFactory;
use Mwop\Blog\Handler\DisplayPostHandler;
use Phly\ConfigFactory\ConfigFactory;
use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Predis\Client as PredisClient;
use Psr\Cache\CacheItemPoolInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use ZendHQ\JobQueue\JobQueue;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'authentication'            => $this->getAuthenticationConfig(),
            'dependencies'              => $this->getDependencies(),
            'cache'                     => $this->getCacheConfig(),
            'content-security-policy'   => [],
            'file-storage'              => $this->getFileStorageConfig(),
            'jq'                        => $this->getJobQueueConfig(),
            'mail'                      => $this->getMailConfig(),
            'mezzio-authorization-rbac' => $this->getAuthorizationConfig(),
            'redis'                     => $this->getRedisConfig(),
        ];
    }

    public function getDependencies(): array
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        return [
            'aliases'    => [
                AuthenticationInterface::class => AuthenticationAdapter::class,
                AuthorizationInterface::class  => LaminasRbac::class,
            ],
            'invokables' => [
                MapperBuilder::class                        => MapperBuilder::class,
                Middleware\RedirectsMiddleware::class       => Middleware\RedirectsMiddleware::class,
                Middleware\XClacksOverheadMiddleware::class => Middleware\XClacksOverheadMiddleware::class,
                Middleware\XPoweredByMiddleware::class      => Middleware\XPoweredByMiddleware::class,
            ],
            'factories'  => [
                AuthenticationAdapter::class                 => AuthenticationAdapterFactory::class,
                'config-authentication'                      => ConfigFactory::class,
                'config-cache'                               => ConfigFactory::class,
                'config-content-security-policy'             => ConfigFactory::class,
                'config-file-storage'                        => ConfigFactory::class,
                'config-mail.transport'                      => ConfigFactory::class,
                'config-redis'                               => ConfigFactory::class,
                Csp::class                                   => Middleware\ContentSecurityPolicyMiddlewareFactory::class,
                CacheItemPoolInterface::class                => Factory\CachePoolFactory::class,
                EventDispatcherInterface::class              => Factory\EventDispatcherFactory::class,
                EventDispatcher\DeferredEventListener::class => EventDispatcher\DeferredEventListenerFactory::class,
                FeedReaderHttpClientInterface::class         => Feed\HttpPlugClientFactory::class,
                Handler\AdminPageHandler::class              => Handler\PageHandlerFactory::class,
                Handler\ClearResponseCacheHandler::class     => Handler\ClearResponseCacheHandlerFactory::class,
                Handler\HomePageHandler::class               => Handler\HomePageHandlerFactory::class,
                Handler\LoginHandler::class                  => Handler\LoginHandlerFactory::class,
                Handler\PingHandler::class                   => Handler\PingHandlerFactory::class,
                Handler\PrivacyPolicyPageHandler::class      => Handler\PageHandlerFactory::class,
                Handler\ResumePageHandler::class             => Handler\PageHandlerFactory::class,
                HomePageCacheExpiration::class               => HomePageCacheExpirationFactory::class,
                JobQueue::class                              => InvokableFactory::class,
                LoggerInterface::class                       => Factory\LoggerFactory::class,
                'mail.transport'                             => Factory\MailTransport::class,
                Middleware\CacheMiddleware::class            => Middleware\CacheMiddlewareFactory::class,
                Middleware\RedirectAmpPagesMiddleware::class => Middleware\RedirectAmpPagesMiddlewareFactory::class,
                PredisClient::class                          => Factory\PredisClientFactory::class,
                ResponseCachePool::class                     => Factory\ResponseCachePoolFactory::class,
                S3Client::class                              => Factory\S3ClientFactory::class,
                SessionCachePool::class                      => SessionCachePoolFactory::class,
                UserRepositoryInterface::class               => UserRepositoryFactory::class,
            ],
            'delegators' => [
                AttachableListenerProvider::class => [
                    EventDispatcher\DeferredEventListenerDelegator::class,
                ],
                DisplayPostHandler::class         => [
                    Middleware\DisplayBlogPostHandlerDelegator::class,
                ],
                Engine::class                     => [
                    Factory\PlatesFunctionsDelegator::class,
                ],
            ],
        ];
        // phpcs:enable Generic.Files.LineLength.TooLong
    }

    public function getAuthenticationConfig(): array
    {
        return [
            // Form fields
            'username' => 'username',
            'password' => 'password',
            'redirect' => '/login',
            // Credentials
            'allowed_credentials' => [
                'username' => null,
                'password' => null,
            ],
        ];
    }

    public function getAuthorizationConfig(): array
    {
        return [
            'roles'       => [
                'admin' => [],
            ],
            'permissions' => [
                'admin' => [
                    'admin',
                    'admin.clear-response-cache',
                ],
            ],
        ];
    }

    public function getCacheConfig(): array
    {
        return [
            'enabled' => true,
        ];
    }

    public function getFileStorageConfig(): array
    {
        return [
            'endpoint' => '',
            'region'   => '',
            'key'      => '',
            'secret'   => '',
            'bucket'   => '',
        ];
    }

    public function getJobQueueConfig(): array
    {
        return [
            'workerUrl' => 'http://nginx/jq/worker',
        ];
    }

    public function getMailConfig(): array
    {
        return [
            'transport' => [
                'apikey' => '',
            ],
        ];
    }

    public function getRedisConfig(): array
    {
        return [
            'connection-parameters' => [
                'scheme' => 'tcp',
                'host'   => 'localhost',
                'port'   => 6379,
            ],
        ];
    }

    public function registerRoutes(Application $app): void
    {
        $app->get('/', Handler\HomePageHandler::class, 'home');
        $app->get('/resume', [
            Middleware\CacheMiddleware::class,
            Handler\ResumePageHandler::class,
        ], 'resume');
        $app->get('/privacy-policy', [
            Middleware\CacheMiddleware::class,
            Handler\PrivacyPolicyPageHandler::class,
        ], 'privacy-policy');
        $app->get('/api/ping', Handler\PingHandler::class, 'api.ping');

        // Admin
        $adminMiddleware = [
            SessionMiddleware::class,
            AuthenticationMiddleware::class,
            AuthorizationMiddleware::class,
        ];
        $app->get(
            '/admin',
            [...$adminMiddleware, Handler\AdminPageHandler::class],
            'admin'
        );
        $app->get(
            '/admin/clear-response-cache',
            [...$adminMiddleware, Handler\ClearResponseCacheHandler::class],
            'admin.clear-response-cache'
        );

        // Authentication
        $app->get('/login', [
            SessionMiddleware::class,
            Handler\LoginHandler::class,
        ], 'login');
        $app->post('/login', [
            SessionMiddleware::class,
            Handler\LoginHandler::class,
        ]);

        $app->get('/logout', [
            SessionMiddleware::class,
            Handler\LogoutHandler::class,
        ], 'logout');
    }
}
