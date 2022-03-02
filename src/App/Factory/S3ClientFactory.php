<?php

declare(strict_types=1);

namespace Mwop\App\Factory;

use Aws\Handler\GuzzleV6\GuzzleHandler;
use Aws\S3\S3Client;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use Psr\Container\ContainerInterface;

class S3ClientFactory
{
    public function __invoke(ContainerInterface $container): S3Client
    {
        $config = $container->get('config-file-storage');

        $httpClient = new Client([
            'handler' => HandlerStack::create(new CurlHandler()),
        ]);

        return new S3Client([
            'version'      => 'latest',
            'endpoint'     => $config['endpoint'],
            'region'       => $config['region'],
            'credentials'  => [
                'key'    => $config['key'],
                'secret' => $config['secret'],
            ],
            'http_handler' => new GuzzleHandler($httpClient),
        ]);
    }
}
