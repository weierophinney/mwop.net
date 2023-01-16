<?php

declare(strict_types=1);

namespace Mwop\Mastodon;

use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Webmozart\Assert\Assert;

final class PsrApiClientFactory
{
    public function __invoke(ContainerInterface $container): PsrApiClient
    {
        $config = $container->get('config');
        Assert::isMap($config);
        Assert::keyExists($config, 'mastodon', 'Missing mastodon configuration key');
        Assert::isMap($config['mastodon'], 'Invalid mastodon configuration; not a map');

        $config = $config['mastodon'];
        Assert::keyExists($config, 'domain', 'Missing mastodon.domain configuration');
        Assert::stringNotEmpty($config['domain'], 'Empty mastodon.domain configuration');

        return new PsrApiClient(
            $container->get(ClientInterface::class),
            $container->get(RequestFactoryInterface::class),
            $container->get(StreamFactoryInterface::class),
            $config['domain'],
        );
    }
}
