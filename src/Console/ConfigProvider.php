<?php

declare(strict_types=1);

namespace Mwop\Console;

use PhlyComic\Console\FetchAllComics;
use PhlyComic\Console\FetchComic;
use PhlyComic\Console\ListComics;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'laminas-cli'  => $this->getConsoleConfig(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'invokables' => [
                ClearCache::class     => ClearCache::class,
                FetchAllComics::class => FetchAllComics::class,
                FetchComic::class     => FetchComic::class,
                ListComics::class     => ListComics::class,
            ],
            'factories'  => [
                FetchComicsCommand::class => FetchComicsCommandFactory::class,
            ],
        ];
    }

    public function getConsoleConfig(): array
    {
        return [
            'commands' => [
                'comics:list'      => ListComics::class,
                'comics:fetch'     => FetchComic::class,
                'comics:fetch-all' => FetchAllComics::class,
                'comics:for-site'  => FetchComicsCommand::class,
            ],
        ];
    }
}
