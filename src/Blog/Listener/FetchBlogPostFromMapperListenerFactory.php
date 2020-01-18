<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Blog\Listener;

use Mwop\Blog\Mapper\MapperInterface;
use Psr\Container\ContainerInterface;

class FetchBlogPostFromMapperListenerFactory
{
    public function __invoke(ContainerInterface $container): FetchBlogPostFromMapperListener
    {
        return new FetchBlogPostFromMapperListener(
            $container->get(MapperInterface::class)
        );
    }
}
