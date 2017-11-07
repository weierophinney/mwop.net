<?php

namespace Mwop\Factory;

use Psr\Container\ContainerInterface;
use Middlewares\Csp;
use ParagonIE\CSPBuilder\CSPBuilder;

class ContentSecurityPolicyFactory
{
    public function __invoke(ContainerInterface $container) : Csp
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $config = $config['content-security-policy'] ?? [];
        return new Csp(new CSPBuilder($config));
    }
}
