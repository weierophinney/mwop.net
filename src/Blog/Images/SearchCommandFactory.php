<?php

declare(strict_types=1);

namespace Mwop\Blog\Images;

use Psr\Container\ContainerInterface;

class SearchCommandFactory
{
    public function __invoke(ContainerInterface $container): SearchCommand
    {
        return new SearchCommand(
            $container->get(Images::class)
        );
    }
}
