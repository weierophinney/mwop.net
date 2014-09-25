<?php
namespace Mwop\Factory;

use Mwop\ComicsPage as Middleware;

class ComicsPage
{
    public function __invoke($services)
    {
        return new Middleware(
            '/comics',
            'comics.page'
        );
    }
}
