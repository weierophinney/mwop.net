<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\App\Middleware;

use Mwop\Blog\Handler\DisplayPostHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Mezzio\MiddlewareFactory;

class DisplayBlogPostHandlerDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $callback
    ) : MiddlewareInterface {
        $factory = $container->get(MiddlewareFactory::class);

        return $factory->pipeline(
            RedirectAmpPagesMiddleware::class,
            $callback()
        );
    }
}
