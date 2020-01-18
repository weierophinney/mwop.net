<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Console;

use Psr\Container\ContainerInterface;

class InstagramFeedFactory
{
    public function __invoke(ContainerInterface $container): InstagramFeed
    {
        return new InstagramFeed($container->get(InstagramClient::class));
    }
}
