<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\App\Handler;

use Psr\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class HomePageHandlerFactory
{
    public function __invoke(ContainerInterface $container) : HomePageHandler
    {
        $config = $container->get('config');
        return new HomePageHandler(
            $config['homepage']['posts'] ?? [],
            $config['instagram']['feed'] ?? '',
            $container->get(TemplateRendererInterface::class)
        );
    }
}
