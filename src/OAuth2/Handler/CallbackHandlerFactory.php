<?php // phpcs:disable Generic.PHP.DiscourageGoto.Found

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

class CallbackHandlerFactory
{
    public function __invoke(ContainerInterface $container): CallbackHandler
    {
        $config = $container->get('config');

        return new CallbackHandler(
            responseFactory: $container->get(ResponseFactoryInterface::class),
            providerFactory: $container->get(ProviderFactory::class),
            renderer: $container->get(TemplateRendererInterface::class),
            isDebug: $config['debug'] ?? false,
        );
    }
}
