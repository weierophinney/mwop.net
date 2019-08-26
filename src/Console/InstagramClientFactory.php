<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\Console;

use Psr\Container\ContainerInterface;

class InstagramClientFactory
{
    public function __invoke(ContainerInterface $container) : InstagramClient
    {
        $config         = $container->get('config');
        $config         = $config['instagram'] ?? [];
        $truncatedDebug = array_key_exists('truncated_debug', $config)
            ? $config['truncated_debug']
            : true;

        return new InstagramClient(
            $config['username'],
            $config['password'],
            $config['debug'] ?? false,
            $truncatedDebug
        );
    }
}
