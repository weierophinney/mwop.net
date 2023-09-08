<?php

declare(strict_types=1);

namespace Mwop\App\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class AdminRebuildHandlerFactory
{
    public function __invoke(ContainerInterface $container) : AdminRebuildHandler
    {
        return new AdminRebuildHandler($container->get(TemplateRendererInterface::class));
    }
}
