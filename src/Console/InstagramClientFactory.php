<?php // phpcs:disable Generic.PHP.DiscourageGoto.Found

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Console;

use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

use function getcwd;

class InstagramClientFactory
{
    public function __invoke(ContainerInterface $container): InstagramClient
    {
        $config = $container->get('config');
        $config = $config['instagram'] ?? [];

        $cachePath = $config['cache_path'] ?? getcwd() . '/data/cache/instagram';

        return new InstagramClient(
            login: $config['login'],
            password: $config['password'],
            profile: $config['profile'],
            cachePool: new FilesystemAdapter('Insta', 0, $cachePath),
            debug: $config['debug'] ?? false,
        );
    }
}
