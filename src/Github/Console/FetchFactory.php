<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Github\Console;

use Interop\Container\ContainerInterface;
use Mwop\Github\AtomReader;

class FetchFactory
{
    public function __invoke(ContainerInterface $container) : Fetch
    {
        return new Fetch($container->get(AtomReader::class));
    }
}
