<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog\Console;

use Mwop\Blog\DisplayPostMiddleware;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Handler\NotFoundHandler;
use Zend\Expressive\Helper\ServerUrlHelper;
use Zend\Expressive\Router\RouterInterface;

class CachePostsFactory
{
    use RoutesTrait;

    public function __invoke(ContainerInterface $container) : CachePosts
    {
        // Ensure that routes are seeded for purposes of dispatching blog
        // posts.
        $this->seedRoutes($container->get(RouterInterface::class));

        // Create and return the cache posts middleware.
        return new CachePosts(
            $container->get(DisplayPostMiddleware::class),
            $container->get(NotFoundHandler::class),
            $container->get(ServerUrlHelper::class)
        );
    }
}
