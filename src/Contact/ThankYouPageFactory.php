<?php
namespace Mwop\Contact;

class ThankYouPageFactory
{
    public function __invoke($services)
    {
        $renderer = $services->get('renderer');
        return new ThankYouPage($renderer, '/', 'contact.thankyou');
    }
}
