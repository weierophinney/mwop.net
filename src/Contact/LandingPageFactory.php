<?php
namespace Mwop\Contact;

class LandingPageFactory
{
    public function __invoke($services)
    {
        return new LandingPage(
            $services->get('Zend\Expressive\Template\TemplateRendererInterface'),
            $services->get('session'),
            $services->get('Config')['contact']
        );
    }
}
