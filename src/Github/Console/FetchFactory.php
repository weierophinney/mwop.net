<?php
namespace Mwop\Github\Console;

use Interop\Container\ContainerInterface;
use Mwop\Github\AtomReader;

class FetchFactory
{
    public function __invoke(ContainerInterface $container) : Fetch
    {
        return new Fetch($container->get(AtomReader::class));
    }
}
