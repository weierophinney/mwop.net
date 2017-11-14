<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Phly\OAuth2ClientAuthentication;

use Psr\Container\ContainerInterface;
use League\OAuth2\Client\Provider;
use RuntimeException;

class OAuth2ProviderFactory
{
    const PROVIDER_MAP = [
        'debug'  => Debug\DebugProvider::class,
        'github' => Provider\Github::class,
        'google' => Provider\Google::class,
    ];

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function createProvider(string $name) : Provider\AbstractProvider
    {
        if (! in_array($name, array_keys(self::PROVIDER_MAP), true)) {
            throw new RuntimeException(sprintf(
                'Unsupported OAuth2 provider "%s"',
                $name
            ));
        }

        $config   = $this->container->get('config')['oauth2'];
        $provider = self::PROVIDER_MAP[$name];

        return new $provider($config[$name]);
    }
}
