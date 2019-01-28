<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\App\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class RedirectAmpPagesMiddlewareFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new RedirectAmpPagesMiddleware(
            $container->get(ResponseFactoryInterface::class)
        );
    }
}
