<?php
namespace Mwop\Factory;

use Mwop\Page as Middleware;

class HomePage
{
    public function __invoke($services)
    {
        $renderer = $services->get('renderer');
        return new Middleware($renderer, '/', 'home');
    }
}
