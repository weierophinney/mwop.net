<?php

declare(strict_types=1);

namespace Mwop\OAuth2;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class RequestAuthenticationHandlerFactory
{
    public function __invoke(ContainerInterface $container) : RequestAuthenticationHandler
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
