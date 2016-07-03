<?php
namespace Mwop\Github\Console;

class FetchFactory
{
    public function __invoke($container)
    {
        return new Fetch($container->get('Mwop\Github\AtomReader'));
    }
}
