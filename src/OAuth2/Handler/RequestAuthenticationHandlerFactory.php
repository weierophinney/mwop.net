<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\OAuth2\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Mwop\OAuth2\Provider\ProviderFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class RequestAuthenticationHandlerFactory
{
    public function __invoke(ContainerInterface $container): RequestAuthenticationHandler
    {
        $config = $container->get('config');

        return new RequestAuthenticationHandler(
            $container->get(ResponseFactoryInterface::class),
            $container->get(ProviderFactory::class),
            $container->get(TemplateRendererInterface::class),
            $config['debug'] ?? false
        );
    }
}
