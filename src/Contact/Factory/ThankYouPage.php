<?php
namespace Mwop\Contact\Factory;

use Mwop\Contact\ThankYouPage as Page;

class ThankYouPage
{
    public function __invoke($services)
    {
        $renderer = $services->get('renderer');
        return new Page($renderer, '/', 'contact.thankyou');
    }
}
