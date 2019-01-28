<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\Contact\Handler;

use Psr\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class DisplayContactFormHandlerFactory
{
    public function __invoke(ContainerInterface $container) : DisplayContactFormHandler
    {
        return new DisplayContactFormHandler(
            $container->get(TemplateRendererInterface::class),
            $container->get('config')['contact']
        );
    }
}
