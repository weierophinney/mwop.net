<?php

declare(strict_types=1);

namespace Mwop\Feed;

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
            'factories'  => [
                Console\FeedAggregator::class => Console\FeedAggregatorFactory::class,
            ],
        ];
    }

    public function getConsoleConfig(): array
    {
        return [
            'commands' => [
                'homepage-feeds' => Console\FeedAggregator::class,
            ],
        ];
    }
}
