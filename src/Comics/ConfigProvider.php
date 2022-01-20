<?php

declare(strict_types=1);

namespace Mwop\Comics;

use Mezzio\Application;
use Mezzio\Authentication\AuthenticationMiddleware;
use Mezzio\Authorization\AuthorizationMiddleware;
use Mezzio\Session\SessionMiddleware;
use Mezzio\Swoole\Task\DeferredServiceListenerDelegator;
use Phly\ConfigFactory\ConfigFactory;
use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;
use PhlyComic\Console\FetchAllComics;
use PhlyComic\Console\FetchComic;
use PhlyComic\Console\ListComics;

use function getcwd;
use function realpath;
use function sprintf;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'comics'                    => $this->getComicsConfig(),
            'dependencies'              => $this->getDependencies(),
            'laminas-cli'               => $this->getConsoleConfig(),
            'mezzio-authorization-rbac' => $this->getAuthorizationConfig(),
        ];
    }

    public function getAuthorizationConfig(): array
    {
        return [
            'permissions' => [
                'admin' => [
                    'comics',
                ],
            ],
        ];
    }

    public function getComicsConfig(): array
    {
        return [
            'exclusions'  => [
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

    public function getConsoleConfig(): array
    {
        return [
            'commands' => [
                'comics:list'      => ListComics::class,
                'comics:fetch'     => FetchComic::class,
                'comics:fetch-all' => FetchAllComics::class,
                'comics:for-site'  => Console\FetchComicsCommand::class,
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            'factories'  => [
                'config-comics'                   => ConfigFactory::class,
                Console\FetchComicsCommand::class => Console\FetchComicsCommandFactory::class,
                FetchComics::class                => FetchComicsFactory::class,
                Handler\ComicsPageHandler::class  => Handler\ComicsPageHandlerFactory::class,
            ],
            'invokables' => [
                FetchAllComics::class => FetchAllComics::class,
                FetchComic::class     => FetchComic::class,
                ListComics::class     => ListComics::class,
            ],
            'delegators' => [
                AttachableListenerProvider::class => [
                    FetchComicsDelegator::class,
                ],
                FetchComics::class                => [
                    DeferredServiceListenerDelegator::class,
                ],
            ],
        ];
    }

    public function registerRoutes(Application $app): void
    {
        $app->get('/comics', [
            SessionMiddleware::class,
            AuthenticationMiddleware::class,
            AuthorizationMiddleware::class,
            Handler\ComicsPageHandler::class,
        ], 'comics');
    }
}
