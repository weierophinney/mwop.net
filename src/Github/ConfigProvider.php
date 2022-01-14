<?php

declare(strict_types=1);

namespace Mwop\Github;

use League\Plates\Engine;
use Phly\ConfigFactory\ConfigFactory;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'github'       => $this->getConfig(),
            'dependencies' => $this->getDependencies(),
        ];
    }

    public function getConfig(): array
    {
        return [
            'user'      => '',
            'limit'     => 10,
            'list_file' => getcwd() . '/data/github-feed.json',
        ];
    }

    public function getDependencies(): array
    {
        return [
            'delegators' => [
                Engine::class => [
                    RenderLinksDelegator::class,
                ],
            ],
            'factories' => [
                AtomReader::class    => AtomReaderFactory::class,
                'config-github'      => ConfigFactory::class,
                Console\Fetch::class => Console\FetchFactory::class,
            ],
        ];
    }
}
