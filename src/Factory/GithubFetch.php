<?php
namespace Mwop\Factory;

use Mwop\Github\Fetch;

class GithubFetch
{
    public function __invoke($services)
    {
        return new Fetch($services->get('Mwop\Github\AtomReader'));
    }
}
