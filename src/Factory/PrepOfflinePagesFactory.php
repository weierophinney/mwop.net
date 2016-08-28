<?php
namespace Mwop\Factory;

use Mwop\Blog\Mapper;
use Mwop\Console\PrepOfflinePages;

class PrepOfflinePagesFactory
{
    public function __invoke($services, $canonicalName, $requestedName)
    {
        return new PrepOfflinePages($services->get(Mapper::class));
    }
}
