<?php
namespace Mwop\Contact;

use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class ThankYouPageFactory
{
    public function __invoke($services)
    {
        return new ThankYouPage(
            $services->get(TemplateRendererInterface::class),
            $services->get(RouterInterface::class)
        );
    }
}
