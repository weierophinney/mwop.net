<?php

declare(strict_types=1);

namespace Mwop\App\Handler;

use Mwop\App\MastodonFeed;
use Mezzio\Template\TemplateRendererInterface;
use Mwop\Art\PhotoMapper;
use Psr\Container\ContainerInterface;

class HomePageHandlerFactory
{
    public function __invoke(ContainerInterface $container): HomePageHandler
    {
        return new HomePageHandler(
            photos: $container->get(PhotoMapper::class),
            mastodonFeed: $container->get(MastodonFeed::class),
            renderer: $container->get(TemplateRendererInterface::class),
        );
    }
}
