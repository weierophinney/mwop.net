<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\OAuth2\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Mezzio\Template\TemplateRendererInterface;

class CheckAuthenticationMiddlewareFactory
{
    public function __invoke(ContainerInterface $container) : CheckAuthenticationMiddleware
    {
        $config = $container->get('config');
        return new CheckAuthenticationMiddleware(
            $container->get(TemplateRendererInterface::class),
            $container->get(ResponseFactoryInterface::class),
            $config['debug'] ?? false
        );
    }
}
