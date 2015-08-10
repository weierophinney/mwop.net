<?php
namespace Mwop\Factory;

use Mwop\Page as Middleware;

class HomePage
{
    public function __invoke($services)
    {
        return new Middleware('/', 'home');
    }
}
