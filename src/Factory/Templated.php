<?php
namespace Mwop\Factory;

use Mwop\Templated as Middleware;

class Templated
{
    public function __invoke($services)
    {
        return new Middleware($services->get('renderer'));
    }
}
