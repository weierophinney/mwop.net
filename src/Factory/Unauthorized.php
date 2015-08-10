<?php
namespace Mwop\Factory;

use Mwop\Unauthorized as Middleware;

class Unauthorized
{
    public function __invoke($services)
    {
        return new Middleware($services->get('renderer'));
    }
}
