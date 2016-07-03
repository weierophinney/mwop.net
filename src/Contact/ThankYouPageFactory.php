<?php
namespace Mwop\Contact;

use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class ThankYouPageFactory
{
    public function __invoke($container)
    {
        return new ThankYouPage(
            $container->get(TemplateRendererInterface::class),
            $container->get(RouterInterface::class)
        );
    }
}
