<?php
namespace Mwop\Factory;

use Mwop\Blog\Mapper;
use Mwop\Console\PrepOfflinePages;

class PrepOfflinePagesFactory
{
    public function __invoke($container, $canonicalName, $requestedName)
    {
        return new PrepOfflinePages($container->get(Mapper::class));
    }
}
