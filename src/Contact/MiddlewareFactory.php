<?php
namespace Mwop\Contact;

use Zend\Expressive\AppFactory;

class MiddlewareFactory
{
    public function __invoke($services)
    {
        error_log(sprintf("In %s", __CLASS__));
        $contact = AppFactory::create($services);

        $contact->get('', 'Mwop\Contact\LandingPage');
        $contact->get('/', 'Mwop\Contact\LandingPage');
        $contact->post('/process', 'Mwop\Contact\Process');
        $contact->get('/thank-you', 'Mwop\Contact\ThankYouPage');

        return $contact;
    }
}
