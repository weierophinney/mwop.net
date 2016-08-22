<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

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
    ) : CachingMiddleware {
        $config = $container->get('config')['blog'];

        return new CachingMiddleware(
            $callback(),
            $config['cache_path'],
            $config['cache_enabled']
        );
    }
}
