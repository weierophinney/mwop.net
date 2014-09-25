<?php
namespace Mwop\Factory;

use Mwop\Page as Middleware;

class ResumePage
{
    public function __invoke($services)
    {
        return new Middleware('/resume', 'resume');
    }
}
