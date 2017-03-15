<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog\Console;

use Interop\Container\ContainerInterface;

class TagCloudFactory
{
    public function __invoke(ContainerInterface $container) : TagCloud
    {
        return new TagCloud($container->get('Mwop\Blog\Mapper'));
    }
}
