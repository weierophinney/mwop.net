<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\App\Feed;

use Http\Client\HttpClient;
use Laminas\Feed\Reader\Http\ClientInterface as FeedReaderHttpClientInterface;
use Laminas\Feed\Reader\Http\Psr7ResponseDecorator;
use Laminas\Feed\Reader\Http\ResponseInterface;
use Psr\Http\Message\RequestFactoryInterface;

class HttpPlugClient implements FeedReaderHttpClientInterface
{
    /** @var HttpClient */
    private $client;

    /** @var RequestFactoryInterface */
    private $requestFactory;

    public function __construct(HttpClient $client, RequestFactoryInterface $requestFactory)
    {
        $this->client         = $client;
        $this->requestFactory = $requestFactory;
    }

    /**
     * @param string $uri
     */
    public function get($uri): ResponseInterface
    {
        $request = $this->requestFactory->createRequest('GET', $uri)
            ->withHeader('User-Agent', 'HTTPie/0.9.2');
        return new Psr7ResponseDecorator($this->client->sendRequest($request));
    }
}
