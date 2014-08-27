<?php
namespace Mwop\Contact\Factory;

use Mwop\Contact\Middleware;

class Contact
{
    public function __invoke($services)
    {
        return new Middleware(
            $services->get('contact.landing'),
            $services->get('contact.process'),
            $services->get('contact.thankyou')
        );
    }
}
