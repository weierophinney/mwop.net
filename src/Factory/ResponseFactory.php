<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Factory;

use Psr\Container\ContainerInterface;
use Zend\Diactoros\Response;

class ResponseFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new Response();
    }
}
