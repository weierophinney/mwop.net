<?php

declare(strict_types=1);

namespace Mwop\Comics\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Mwop\App\Handler\PageHandler;
use Psr\Container\ContainerInterface;

class ComicsPageHandlerFactory
{
    public function __invoke(ContainerInterface $container): PageHandler
    {
        return new PageHandler(
            'mwop::comics.page',
            $container->get(TemplateRendererInterface::class)
        );
    }
}
