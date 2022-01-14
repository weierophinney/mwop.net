<?php

declare(strict_types=1);

namespace Mwop\Github\Console;

use Mwop\Github\AtomReader;
use Mwop\Github\ItemList;
use Psr\Container\ContainerInterface;

class FetchFactory
{
    public function __invoke(ContainerInterface $container): Fetch
    {
        return new Fetch(
            reader: $container->get(AtomReader::class),
            itemList: $container->get(ItemList::class),
        );
    }
}
