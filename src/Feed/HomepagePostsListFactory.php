<?php

declare(strict_types=1);

namespace Mwop\Feed;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class HomepagePostsListFactory
{
    public function __invoke(ContainerInterface $container): HomepagePostsList
    {
        $config = $container->get('config-feeds');
        return new HomepagePostsList(
            limit: $config['feed-count'],
            logger: $container->get(LoggerInterface::class),
        );
    }
}
