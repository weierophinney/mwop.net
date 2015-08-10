<?php
namespace Mwop\Factory;

use Mwop\CachePosts as Command;

class CachePosts
{
    public function __invoke($services)
    {
        return new Command($services->get('Mwop\Blog\Middleware'));
    }
}
