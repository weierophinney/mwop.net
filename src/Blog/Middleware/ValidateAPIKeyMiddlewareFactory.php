<?php

declare(strict_types=1);

namespace Mwop\Blog\Middleware;

use Psr\Container\ContainerInterface;

class ValidateAPIKeyMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): ValidateAPIKeyMiddleware
    {
        $config = $container->get('config-blog.api');
        return new ValidateAPIKeyMiddleware($config['key']);
    }
}
