<?php // phpcs:disable Generic.PHP.DiscourageGoto.Found


declare(strict_types=1);

namespace Mwop\Contact\Handler;

use Mezzio\Helper\UrlHelper;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class ProcessContactFormHandlerFactory
{
    public function __invoke(ContainerInterface $container): ProcessContactFormHandler
    {
        return new ProcessContactFormHandler(
            dispatcher: $container->get(EventDispatcherInterface::class),
            template: $container->get(TemplateRendererInterface::class),
            urlHelper: $container->get(UrlHelper::class),
            config: $container->get('config-contact'),
        );
    }
}
