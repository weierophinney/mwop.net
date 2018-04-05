<?php

declare(strict_types=1);

namespace Mwop\Blog;

use Psr\Container\ContainerInterface;
use Zend\Expressive\Helper\UrlHelper;

class SearchHandlerFactory
{
    public function __invoke(ContainerInterface $container) : SearchHandler
    {
        return new SearchHandler(
            $container->get(__NAMESPACE__ . '\Mapper'),
            $container->get(UrlHelper::class)
        );
    }
}
