<?php
namespace Mwop\Factory;

use Mwop\View as Middleware;

class View
{
    public function __invoke($services)
    {
        return new View($services->get('renderer'));
    }
}
