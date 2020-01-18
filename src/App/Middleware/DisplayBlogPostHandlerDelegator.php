<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\App\Middleware;

use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

class DisplayBlogPostHandlerDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $callback
    ): MiddlewareInterface {
        $factory = $container->get(MiddlewareFactory::class);

        return $factory->pipeline(
            RedirectAmpPagesMiddleware::class,
            $callback()
        );
    }
}
