<?php

declare(strict_types=1);

namespace Mwop\Github;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class ItemListFactory
{
    public function __invoke(ContainerInterface $container): ItemList
    {
        $config = $container->get('config-github');
        return new ItemList(
            $config['list_file'],
            $config['limit'],
            $container->get(LoggerInterface::class)
        );
    }
}
