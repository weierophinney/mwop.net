<?php
namespace Mwop\Contact;

use Zend\Expressive\AppFactory;

class MiddlewareFactory
{
    public function __invoke($services)
    {
        $contact = AppFactory::create($services);

        $contact->get('', LandingPage::class);
        $contact->get('/', LandingPage::class);
        $contact->post('/process', Process::class);
        $contact->get('/thank-you', ThankYouPage::class);

        return $contact;
    }
}
