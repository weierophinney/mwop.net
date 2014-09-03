<?php
namespace Mwop\Contact;

class LandingPageFactory
{
    public function __invoke($services)
    {
        $renderer = $services->get('renderer');
        $session  = $services->get('session');
        $config   = $services->get('Config')['contact'];
        return new LandingPage($renderer, '/', 'contact.landing', $session, $config);
    }
}
