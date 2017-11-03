<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Factory;

use Interop\Container\ContainerInterface;
use Mwop\Auth\UserSession;
use Mwop\ComicsPage as Page;
use Zend\Stratigility\MiddlewarePipe;
use Zend\Expressive\Session\SessionMiddleware;
use Zend\Expressive\Template\TemplateRendererInterface;

class ComicsPage
{
    public function __invoke(ContainerInterface $container) : callable
    {
        $pipeline = new MiddlewarePipe();

        $pipeline->pipe($container->get(SessionMiddleware::class));
        $pipeline->pipe($container->get(UserSession::class));
        $pipeline->pipe(new Page(
            $container->get(TemplateRendererInterface::class),
            $container->get('Mwop\UnauthorizedResponseFactory')
        ));

        return $pipeline;
    }
}
