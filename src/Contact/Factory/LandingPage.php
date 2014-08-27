<?php
namespace Mwop\Contact\Factory;

use Mwop\Contact\LandingPage as Page;

class LandingPage
{
    public function __invoke($services)
    {
        $renderer = $services->get('renderer');
        $session  = $services->get('session');
        $config   = $services->get('Config')['contact'];
        return new Page($renderer, '/', 'contact.landing', $session, $config);
    }
}
