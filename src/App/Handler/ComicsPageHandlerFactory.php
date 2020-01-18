<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

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
