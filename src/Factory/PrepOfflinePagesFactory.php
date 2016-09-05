<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Factory;

use Interop\Container\ContainerInterface;
use Mwop\Blog\Mapper;
use Mwop\Console\PrepOfflinePages;

class PrepOfflinePagesFactory
{
    public function __invoke(ContainerInterface $container) : PrepOfflinePages
    {
        return new PrepOfflinePages($container->get(Mapper::class));
    }
}
