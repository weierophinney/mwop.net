<?php
namespace Mwop\Contact;

use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class LandingPageFactory
{
    public function __invoke($services)
    {
        return new LandingPage(
            $services->get(TemplateRendererInterface::class),
            $services->get(RouterInterface::class),
            $services->get('session'),
            $services->get('config')['contact']
        );
    }
}
