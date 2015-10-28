<?php
namespace Mwop\Factory;

use Mwop\ComicsPage as Page;
use Mwop\PageView;
use Zend\Stratigility\MiddlewarePipe;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class ComicsPage
{
    public function __invoke($services)
    {
        $pipeline = new MiddlewarePipe();
        $view     = new PageView();
        $view->setRouter($services->get(RouterInterface::class));

        $pipeline->pipe($services->get('Mwop\Auth\UserSession'));
        $pipeline->pipe(new Page(
            'mwop::comics.page',
            $view,
            $services->get(TemplateRendererInterface::class)
        ));

        return $pipeline;
    }
}
