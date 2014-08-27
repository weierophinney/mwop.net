<?php
namespace Mwop\Contact\Factory;

use Mwop\Contact\Process as Page;

class Process
{
    public function __invoke($services)
    {
        $config = $services->get('Config');
        $config = $config['contact'];

        return new Page(
            $services->get('renderer'),
            $services->get('session'),
            $services->get('mail.transport'),
            'contact.landing',
            $config
        );
    }
}
