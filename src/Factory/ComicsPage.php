<?php
namespace Mwop\Factory;

use Mwop\ComicsPage as Page;

class ComicsPage
{
    public function __invoke($services)
    {
        return new Page(
            'comics.page',
            [],
            $services->get('Mwop\Template\TemplateInterface')
        );
    }
}
