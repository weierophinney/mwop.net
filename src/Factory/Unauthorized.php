<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Factory;

use Interop\Container\ContainerInterface;
use Mwop\Unauthorized as Middleware;
use Zend\Expressive\Template\TemplateRendererInterface;

class Unauthorized
{
    public function __invoke(ContainerInterface $container) : Middleware
    {
        return new Middleware(
            $container->get(TemplateRendererInterface::class)
        );
    }
}
