<?php
namespace Mwop\Contact;

class MiddlewareFactory
{
    public function __invoke($services)
    {
        return new Middleware(
            $services->get('Mwop\Contact\LandingPage'),
            $services->get('Mwop\Contact\Process'),
            $services->get('Mwop\Contact\ThankYouPage')
        );
    }
}
