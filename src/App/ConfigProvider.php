<?php

declare(strict_types=1);

namespace Mwop\App;

use Laminas\Feed\Reader\Http\ClientInterface as FeedReaderHttpClientInterface;
use League\Plates\Engine;
use Mezzio\Application;
use Mezzio\Authentication\AuthenticationInterface;
use Mezzio\Authentication\AuthenticationMiddleware;
use Mezzio\Authentication\UserRepositoryInterface;
use Mezzio\Authorization\AuthorizationInterface;
use Mezzio\Authorization\AuthorizationMiddleware;
use Mezzio\Authorization\Rbac\LaminasRbac;
use Mezzio\Session\SessionMiddleware;
use Mezzio\Swoole\Event\EventDispatcherInterface as SwooleEventDispatcher;
use Mezzio\Swoole\Task\DeferredServiceListenerDelegator;
use Middlewares\Csp;
use Mwop\App\Factory\UserRepositoryFactory;
use Mwop\App\PeriodicTask\ComicsEventListener;
use Mwop\Blog\Handler\DisplayPostHandler;
use Phly\ConfigFactory\ConfigFactory;
use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use Psr\Cache\CacheItemPoolInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'authentication'            => $this->getAuthenticationConfig(),
            'dependencies'              => $this->getDependencies(),
            'cache'                     => $this->getCacheConfig(),
            'comics'                    => $this->getComicsConfig(),
            'content-security-policy'   => [],
            'mail'                      => $this->getMailConfig(),
            'mezzio-authorization-rbac' => $this->getAuthorizationConfig(),
        ];
    }

    public function getDependencies(): array
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
        return [
            'aliases'    => [
                AuthenticationInterface::class => AuthenticationAdapter::class,
                AuthorizationInterface::class  => LaminasRbac::class,
                SwooleEventDispatcher::class   => EventDispatcherInterface::class,
            ],
            'invokables' => [
                Middleware\RedirectsMiddleware::class       => Middleware\RedirectsMiddleware::class,
                Middleware\XClacksOverheadMiddleware::class => Middleware\XClacksOverheadMiddleware::class,
                Middleware\XPoweredByMiddleware::class      => Middleware\XPoweredByMiddleware::class,
            ],
            'factories'  => [
                AuthenticationAdapter::class                 => AuthenticationAdapterFactory::class,
                'config-authentication'                      => ConfigFactory::class,
                'config-cache'                               => ConfigFactory::class,
                'config-comics'                              => ConfigFactory::class,
                'config-content-security-policy'             => ConfigFactory::class,
                'config-mail.transport'                      => ConfigFactory::class,
                Csp::class                                   => Middleware\ContentSecurityPolicyMiddlewareFactory::class,
                CacheItemPoolInterface::class                => Factory\CachePoolFactory::class,
                EventDispatcherInterface::class              => Factory\EventDispatcherFactory::class,
                FeedReaderHttpClientInterface::class         => Feed\HttpPlugClientFactory::class,
                Handler\ComicsPageHandler::class             => Handler\ComicsPageHandlerFactory::class,
                Handler\HomePageHandler::class               => Handler\HomePageHandlerFactory::class,
                Handler\LoginHandler::class                  => Handler\LoginHandlerFactory::class,
                Handler\NowPageHandler::class                => Handler\PageHandlerFactory::class,
                Handler\PingHandler::class                   => Handler\PingHandlerFactory::class,
                Handler\PrivacyPolicyPageHandler::class      => Handler\PageHandlerFactory::class,
                Handler\ResumePageHandler::class             => Handler\PageHandlerFactory::class,
                'mail.transport'                             => Factory\MailTransport::class,
                Middleware\RedirectAmpPagesMiddleware::class => Middleware\RedirectAmpPagesMiddlewareFactory::class,
                PeriodicTask\FetchComics::class              => PeriodicTask\FetchComicsFactory::class,
                SessionCachePool::class                      => SessionCachePoolFactory::class,
                UserRepositoryInterface::class               => UserRepositoryFactory::class,
            ],
            'delegators' => [
                AttachableListenerProvider::class => [
                    Factory\SwooleTaskInvokerListenerDelegator::class,
                    PeriodicTask\SwooleTimerDelegator::class,
                ],
                PeriodicTask\FetchComics::class   => [
                    DeferredServiceListenerDelegator::class,
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
                    'comics',
                ],
            ],
        ];
    }

    public function getCacheConfig(): array
    {
        return [
            'connection-parameters' => [
                'scheme' => 'tcp',
                'host'   => 'localhost',
                'port'   => 6379,
            ],
        ];
    }

    public function getComicsConfig(): array
    {
        return [
            'exclusions' => [
                'bloom-county',
                'dilbert',
                'g-g',
                'goats',
                'listen-tome',
                'nih',
                'pennyarcade',
                'phd',
                'pickles',
                'reptilis-rex',
                'uf',
            ],
            'output_file' => sprintf('%s/data/comics.phtml', realpath(getcwd())),
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

    public function registerRoutes(Application $app): void
    {
        $app->get('/', Handler\HomePageHandler::class, 'home');
        $app->get('/comics', [
            SessionMiddleware::class,
            AuthenticationMiddleware::class,
            AuthorizationMiddleware::class,
            Handler\ComicsPageHandler::class,
        ], 'comics');
        $app->get('/resume', Handler\ResumePageHandler::class, 'resume');
        $app->get('/now', Handler\NowPageHandler::class, 'now');
        $app->get('/privacy-policy', Handler\PrivacyPolicyPageHandler::class, 'privacy-policy');
        $app->get('/api/ping', Handler\PingHandler::class, 'api.ping');

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
