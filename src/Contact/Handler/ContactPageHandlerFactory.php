<?php // phpcs:disable Generic.PHP.DiscourageGoto.Found


declare(strict_types=1);

namespace Mwop\Contact\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class ContactPageHandlerFactory
{
    public function __invoke(ContainerInterface $container): ContactPageHandler
    {
        return new ContactPageHandler(
            template: $container->get(TemplateRendererInterface::class),
        );
    }
}
