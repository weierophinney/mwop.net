<?php
namespace Mwop\Factory;

use Mwop\ComicsPage as Page;
use Zend\Stratigility\MiddlewarePipe;

class ComicsPage
{
    public function __invoke($services)
    {
        $pipeline = new MiddlewarePipe();

        $pipeline->pipe($services->get('Mwop\Auth\UserSession'));
        $pipeline->pipe(new Page(
            'comics.page',
            [],
            $services->get('Zend\Expressive\Template\TemplateInterface')
        ));

        return $pipeline;
    }
}
