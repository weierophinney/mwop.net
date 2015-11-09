<?php
namespace Mwop\Contact;

use Zend\Expressive\Template\TemplateRendererInterface;

class LandingPageFactory
{
    public function __invoke($services)
    {
        return new LandingPage(
            $services->get(TemplateRendererInterface::class),
            $services->get('session'),
            $services->get('config')['contact']
        );
    }
}
