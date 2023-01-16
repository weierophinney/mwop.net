<?php

declare(strict_types=1);

namespace Mwop\Mastodon;

use Mwop\Art\Storage\PhotoRetrieval;
use Psr\Container\ContainerInterface;

final class MediaFactoryFactory
{
    public function __invoke(ContainerInterface $container): MediaFactory
    {
        return new MediaFactory($container->get(PhotoRetrieval::class));
    }
}
