<?php

declare(strict_types=1);

namespace Mwop\Hooks\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class ValidateWebhookRequestMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): ValidateWebhookRequestMiddleware
    {
        $config = $container->get('config-hooks');
        return new ValidateWebhookRequestMiddleware(
            expectedHeader: $config['token-header'],
            expectedToken: $config['token-value'],
            responseFactory: $container->get(ResponseFactoryInterface::class),
        );
    }
}
