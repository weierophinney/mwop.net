<?php
namespace Mwop\Contact;

class LandingPageFactory
{
    public function __invoke($services)
    {
        return new LandingPage(
            $services->get('Mwop\Template\TemplateInterface'),
            $services->get('session'),
            $services->get('Config')['contact']
        );
    }
}
