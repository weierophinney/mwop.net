<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\App\Middleware;

use Psr\Container\ContainerInterface;
use Middlewares\Csp;
use ParagonIE\CSPBuilder\CSPBuilder;

class ContentSecurityPolicyMiddlewareFactory
{
    public function __invoke(ContainerInterface $container) : Csp
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $config = $config['content-security-policy'] ?? [];
        return new Csp(new CSPBuilder($config));
    }
}
