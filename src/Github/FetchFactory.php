<?php
namespace Mwop\Github;

class FetchFactory
{
    public function __invoke($services)
    {
        return new Fetch($services->get('Mwop\Github\AtomReader'));
    }
}
