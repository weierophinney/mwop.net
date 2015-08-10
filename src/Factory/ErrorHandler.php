<?php
namespace Mwop\Factory;

use Mwop\ErrorHandler as Middleware;

class ErrorHandler
{
    public function __invoke($services)
    {
        $config = $services->get('Config');
        return new Middleware($services->get('renderer'), $config['debug']);
    }
}
