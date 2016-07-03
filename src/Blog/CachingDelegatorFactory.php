<?php
namespace Mwop\Blog;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CachingDelegatorFactory implements DelegatorFactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        callable $callback,
        array $options = null
    ) {
        $config = $container->get('config')['blog'];

        return new CachingMiddleware(
            $callback(),
            $config['cache_path'],
            $config['cache_enabled']
        );
    }
}
