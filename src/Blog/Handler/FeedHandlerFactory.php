<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Blog\Handler;

use Mezzio\Middleware\NotFoundHandler;
use Psr\Container\ContainerInterface;

class FeedHandlerFactory
{
    public function __invoke(ContainerInterface $container): FeedHandler
    {
        return new FeedHandler(
            $container->get(NotFoundHandler::class)
        );
    }
}
