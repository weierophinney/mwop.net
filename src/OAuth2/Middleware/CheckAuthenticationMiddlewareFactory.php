<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\OAuth2\Middleware;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class CheckAuthenticationMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): CheckAuthenticationMiddleware
    {
        $config = $container->get('config');
        return new CheckAuthenticationMiddleware(
            $container->get(TemplateRendererInterface::class),
            $container->get(ResponseFactoryInterface::class),
            $config['debug'] ?? false
        );
    }
}
