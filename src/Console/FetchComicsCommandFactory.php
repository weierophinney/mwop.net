<?php

declare(strict_types=1);

namespace Mwop\Console;

use Psr\Container\ContainerInterface;

class FetchComicsCommandFactory
{
    public function __invoke(ContainerInterface $container): FetchComicsCommand
    {
        $config = $container->get('config-comics') ?? [];
        return new FetchComicsCommand(
            exclusions: $config['exclusions'] ?? [],
        );
    }
}
