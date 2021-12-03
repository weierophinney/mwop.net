<?php

declare(strict_types=1);

namespace Mwop\App\Middleware;

use Middlewares\Csp;
use ParagonIE\CSPBuilder\CSPBuilder;
use Psr\Container\ContainerInterface;

class ContentSecurityPolicyMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): Csp
    {
        $config = $container->get('config-content-security-policy');
        return new Csp(new CSPBuilder($config));
    }
}
