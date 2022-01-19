<?php

declare(strict_types=1);

namespace Mwop\App\PeriodicTask;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class FetchComicsFactory
{
    public function __invoke(ContainerInterface $container): FetchComics
    {
        $config = $container->get('config-comics') ?? [];
        return new FetchComics(
            logger: $container->get(LoggerInterface::class),
            exclusions: $config['exclusions'],
            comicsFile: $config['output_file'],
        );
    }
}
