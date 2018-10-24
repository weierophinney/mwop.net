<?php

declare(strict_types=1);

namespace Mwop\OAuth2;

use League\OAuth2\Client\Provider;
use Psr\Container\ContainerInterface;

class ProviderFactory
{
    public const PROVIDER_MAP = [
        'debug'  => DebugProvider::class,
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

    /**
     * @throws Exception\UnsupportedProviderException
     * @throws Exception\MissingProviderConfigException
     */
    public function createProvider(string $name) : Provider\AbstractProvider
    {
        $knownProviders = array_keys(self::PROVIDER_MAP);
        if (! in_array($name, $knownProviders, true)) {
            throw Exception\UnsupportedProviderException::forProvider($name, $knownProviders);
        }

        $config = $this->container->get('config')['oauth2'] ?? [];

        if (! isset($config[$name])) {
            throw Exception\MissingProviderConfigException::forProvider($name);
        }

        $provider = self::PROVIDER_MAP[$name];

        return new $provider($config[$name]);
    }
}
