<?php

declare(strict_types=1);

namespace Mwop\Mastodon;

use Psr\Container\ContainerInterface;

final class CredentialsFactory
{
    public function __invoke(ContainerInterface $container): Credentials
    {
        $config = $container->get('config');
        Assert::isMap($config);
        Assert::keyExists($config, 'mastodon', 'Missing mastodon configuration key');
        Assert::isMap($config['mastodon'], 'Invalid mastodon configuration; not a map');

        $config = $config['mastodon'];

        return new Credentials(
            $config['client_id'] ?? '',
            $config['client_secret'] ?? '',
        );
    }
}
