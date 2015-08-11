<?php
namespace Mwop\Contact;

class LandingPageFactory
{
    public function __invoke($services)
    {
        return new LandingPage(
            $services->get('Zend\Expressive\Template\TemplateInterface'),
            $services->get('session'),
            $services->get('Config')['contact']
        );
    }
}
