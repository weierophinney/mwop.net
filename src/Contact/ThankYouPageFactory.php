<?php
namespace Mwop\Contact;

class ThankYouPageFactory
{
    public function __invoke($services)
    {
        return new ThankYouPage(
            $services->get('Zend\Expressive\Template\TemplateInterface')
        );
    }
}
