<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Factory;

use Interop\Container\ContainerInterface;
use Mwop\NotFound;
use Zend\Expressive\Template\TemplateRendererInterface;

class NotFoundFactory
{
    public function __invoke(ContainerInterface $container) : NotFound
    {
        return new NotFound(
            $container->get('config')['debug'] ?? false,
            $container->get(TemplateRendererInterface::class)
        );
    }
}
