<?php

declare(strict_types=1);

namespace Mwop\App\Handler;

use Mezzio\Template\TemplateRendererInterface;
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
