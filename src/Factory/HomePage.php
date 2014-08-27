<?php
namespace Mwop\Factory;

use Mwop\Page;

class HomePage
{
    public function __invoke($services)
    {
        $renderer = $services->get('renderer');
        return new Page($renderer, '/', 'home');
    }
}
