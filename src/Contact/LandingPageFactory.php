<?php
namespace Mwop\Contact;

class LandingPageFactory
{
    public function __invoke($services)
    {
        $session  = $services->get('session');
        $config   = $services->get('Config')['contact'];
        return new LandingPage('/', 'contact.landing', $session, $config);
    }
}
