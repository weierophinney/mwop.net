<?php // phpcs:disable Generic.PHP.DiscourageGoto.Found

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Contact\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class DisplayContactFormHandlerFactory
{
    public function __invoke(ContainerInterface $container): DisplayContactFormHandler
    {
        return new DisplayContactFormHandler(
            template: $container->get(TemplateRendererInterface::class),
            config: $container->get('config-contact'),
        );
    }
}
