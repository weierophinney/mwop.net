<?php
namespace Mwop\Factory;

use Mwop\ComicsPage as Page;
use Zend\Stratigility\MiddlewarePipe;
use Zend\Expressive\Template\TemplateRendererInterface;

class ComicsPage
{
    public function __invoke($container)
    {
        $pipeline = new MiddlewarePipe();

        $pipeline->pipe($container->get('Mwop\Auth\UserSession'));
        $pipeline->pipe(new Page(
            'mwop::comics.page',
            $container->get(TemplateRendererInterface::class)
        ));

        return $pipeline;
    }
}
