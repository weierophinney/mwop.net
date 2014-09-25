<?php
namespace Mwop\Contact;

class ThankYouPageFactory
{
    public function __invoke($services)
    {
        return new ThankYouPage('/', 'contact.thankyou');
    }
}
