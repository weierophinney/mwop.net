<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\Contact\Handler;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Template\TemplateRendererInterface;

class ProcessContactFormHandlerFactory
{
    public function __invoke(ContainerInterface $container) : ProcessContactFormHandler
    {
        return new ProcessContactFormHandler(
            $container->get(EventDispatcherInterface::class),
            $container->get(TemplateRendererInterface::class),
            $container->get(UrlHelper::class),
            $container->get('config')['contact']
        );
    }
}
