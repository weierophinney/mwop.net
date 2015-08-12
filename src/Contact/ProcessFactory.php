<?php
namespace Mwop\Contact;

class ProcessFactory
{
    public function __invoke($services)
    {
        return new Process(
            $services->get('session'),
            $services->get('mail.transport'),
            $services->get('Zend\Expressive\Template\TemplateInterface'),
            $services->get('Config')['contact']
        );
    }
}
