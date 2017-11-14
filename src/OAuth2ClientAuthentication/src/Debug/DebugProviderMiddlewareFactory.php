<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Phly\OAuth2ClientAuthentication\Debug;

use Phly\OAuth2ClientAuthentication\RedirectResponseFactory;
use Psr\Container\ContainerInterface;

class DebugProviderMiddlewareFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new DebugProviderMiddleware($container->get(RedirectResponseFactory::class));
    }
}
