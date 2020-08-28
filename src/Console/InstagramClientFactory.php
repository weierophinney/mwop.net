<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Console;

use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class InstagramClientFactory
{
    public function __invoke(ContainerInterface $container): InstagramClient
    {
        $config = $container->get('config');
        $config = $config['instagram'] ?? [];

        $cachePath = $config['cache_path'] ?? getcwd() . '/data/cache/instagram';

        return new InstagramClient(
            $config['login'],
            $config['password'],
            $config['profile'],
            new FilesystemAdapter('Insta', 0, $cachePath),
            $config['debug'] ?? false
        );
    }
}
