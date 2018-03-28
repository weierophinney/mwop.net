<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Factory;

use Mwop\Page;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Zend\Expressive\Authentication\AuthenticationMiddleware;
use Zend\Expressive\MiddlewareFactory;
use Zend\Expressive\Session\SessionMiddleware;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Stratigility\MiddlewarePipe;

class ComicsPage
{
    public function __invoke(ContainerInterface $container) : MiddlewareInterface
    {
        $factory = $container->get(MiddlewareFactory::class);
        $pipeline = new MiddlewarePipe();

        $pipeline->pipe($factory->prepare(SessionMiddleware::class));
        $pipeline->pipe($factory->prepare(AuthenticationMiddleware::class));
        $pipeline->pipe($factory->prepare(new Page(
            'mwop::comics.page',
            $container->get(TemplateRendererInterface::class)
        )));

        return $pipeline;
    }
}
