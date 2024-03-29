<?php

declare(strict_types=1);

namespace Mwop\App\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Mwop\Art\PhotoMapper;
use Mwop\Mastodon\Feed;
use Psr\Container\ContainerInterface;

class HomePageHandlerFactory
{
    public function __invoke(ContainerInterface $container): HomePageHandler
    {
        return new HomePageHandler(
            photos: $container->get(PhotoMapper::class),
            mastodonFeed: $container->get(Feed::class),
            renderer: $container->get(TemplateRendererInterface::class),
        );
    }
}
