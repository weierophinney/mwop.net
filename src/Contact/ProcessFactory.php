<?php
namespace Mwop\Contact;

class ProcessFactory
{
    public function __invoke($services)
    {
        $config = $services->get('Config');
        $config = $config['contact'];

        return new Process(
            $services->get('session'),
            $services->get('mail.transport'),
            'contact.landing',
            $config
        );
    }
}
