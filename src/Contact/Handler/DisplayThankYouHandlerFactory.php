<?php // phpcs:disable Generic.PHP.DiscourageGoto.Found


declare(strict_types=1);

namespace Mwop\Contact\Handler;

use Mezzio\Helper\UrlHelper;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class DisplayThankYouHandlerFactory
{
    public function __invoke(ContainerInterface $container): DisplayThankYouHandler
    {
        return new DisplayThankYouHandler(
            template: $container->get(TemplateRendererInterface::class),
            urlHelper: $container->get(UrlHelper::class),
        );
    }
}
