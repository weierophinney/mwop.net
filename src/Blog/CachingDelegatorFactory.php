<?php
namespace Mwop\Blog;

use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CachingDelegatorFactory implements DelegatorFactoryInterface
{
    public function createDelegatorWithName(
        ServiceLocatorInterface $services,
        $name,
        $requestedName,
        $callback
    ) {
        $config = $services->get('Config')['blog'];

        return new CachingMiddleware(
            $callback(),
            $config['cache_path'],
            $config['cache_enabled']
        );
    }
}
