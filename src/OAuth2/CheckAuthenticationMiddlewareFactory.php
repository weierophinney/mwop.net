<?php

declare(strict_types=1);

namespace Mwop\OAuth2;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

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
