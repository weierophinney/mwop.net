<?php
namespace Mwop\Factory;

use Mwop\Blog\MapperInterface;
use Mwop\Console\PrepOfflinePages;
use Zend\Expressive\Router\RouterInterface;

class PrepOfflinePagesFactory
{
    public function __invoke($services, $canonicalName, $requestedName)
    {
        return new PrepOfflinePages(
            $services->get(MapperInterface::class),
            $services->get(RouterInterface::class)
        );
    }
}
