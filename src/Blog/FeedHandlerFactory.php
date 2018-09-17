<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Middleware\NotFoundHandler;

class FeedHandlerFactory
{
    public function __invoke(ContainerInterface $container) : FeedHandler
    {
        return new FeedHandler(
            $container->get(NotFoundHandler::class)
        );
    }
}
