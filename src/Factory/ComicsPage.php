<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Factory;

use Interop\Container\ContainerInterface;
use Mwop\Page;
use Zend\Stratigility\MiddlewarePipe;
use Zend\Expressive\Authentication\AuthenticationMiddleware;
use Zend\Expressive\Session\SessionMiddleware;
use Zend\Expressive\Template\TemplateRendererInterface;

class ComicsPage
{
    public function __invoke(ContainerInterface $container) : callable
    {
        $pipeline = new MiddlewarePipe();

        $pipeline->pipe($container->get(SessionMiddleware::class));
        $pipeline->pipe($container->get(AuthenticationMiddleware::class));
        $pipeline->pipe(new Page(
            'mwop::comics.page',
            $container->get(TemplateRendererInterface::class)
        ));

        return $pipeline;
    }
}
