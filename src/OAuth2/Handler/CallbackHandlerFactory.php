<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\OAuth2\Handler;

use Mwop\OAuth2\Provider\ProviderFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class CallbackHandlerFactory
{
    public function __invoke(ContainerInterface $container) : CallbackHandler
    {
        $config = $container->get('config');

        return new CallbackHandler(
            $container->get(ResponseFactoryInterface::class),
            $container->get(ProviderFactory::class),
            $container->get(TemplateRendererInterface::class),
            $config['debug'] ?? false
        );
    }
}
