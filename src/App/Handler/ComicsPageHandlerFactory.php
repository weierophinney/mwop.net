<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\App\Handler;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Mezzio\Authentication\AuthenticationMiddleware;
use Mezzio\MiddlewareFactory;
use Mezzio\Session\SessionMiddleware;
use Mezzio\Template\TemplateRendererInterface;
use Laminas\Stratigility\MiddlewarePipe;

class ComicsPageHandlerFactory
{
    public function __invoke(ContainerInterface $container) : PageHandler
    {
        return new PageHandler(
            'mwop::comics.page',
            $container->get(TemplateRendererInterface::class)
        );
    }
}
