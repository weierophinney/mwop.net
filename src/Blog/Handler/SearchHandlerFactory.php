<?php

declare(strict_types=1);

namespace Mwop\Blog\Handler;

use Mezzio\Helper\UrlHelper;
use Mwop\Blog\Mapper\MapperInterface;
use Psr\Container\ContainerInterface;

class SearchHandlerFactory
{
    public function __invoke(ContainerInterface $container): SearchHandler
    {
        return new SearchHandler(
            $container->get(MapperInterface::class),
            $container->get(UrlHelper::class)
        );
    }
}
