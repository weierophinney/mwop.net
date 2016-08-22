<?php
namespace Mwop\Contact;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class ThankYouPageFactory
{
    public function __invoke(ContainerInterface $container) : ThankYouPage
    {
        return new ThankYouPage(
            $container->get(TemplateRendererInterface::class),
            $container->get(RouterInterface::class)
        );
    }
}
