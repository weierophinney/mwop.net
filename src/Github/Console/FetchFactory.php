<?php
namespace Mwop\Github\Console;

class FetchFactory
{
    public function __invoke($services)
    {
        return new Fetch($services->get('Mwop\Github\AtomReader'));
    }
}
