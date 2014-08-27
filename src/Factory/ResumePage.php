<?php
namespace Mwop\Factory;

use Mwop\Page;

class ResumePage
{
    public function __invoke($services)
    {
        $renderer = $services->get('renderer');
        return new Page($renderer, '/resume', 'resume');
    }
}
