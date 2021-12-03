<?php // phpcs:disable Generic.PHP.DiscourageGoto.Found


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
            renderer: $container->get(TemplateRendererInterface::class),
            responseFactory: $container->get(ResponseFactoryInterface::class),
            isDebug: $config['debug'] ?? false,
        );
    }
}
