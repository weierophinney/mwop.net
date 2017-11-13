<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace OAuth2Authentication\Debug;

use OAuth2Authentication\RedirectResponseFactory;
use Psr\Container\ContainerInterface;

class DebugProviderMiddlewareFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new DebugProviderMiddleware($container->get(RedirectResponseFactory::class));
    }
}
