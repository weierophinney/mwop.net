<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\App\Feed;

use Http\Client\HttpClient;
use Psr\Http\Message\RequestFactoryInterface;
use Zend\Feed\Reader\Http\ClientInterface as FeedReaderHttpClientInterface;
use Zend\Feed\Reader\Http\Psr7ResponseDecorator;
use Zend\Feed\Reader\Http\ResponseInterface;

class HttpPlugClient implements FeedReaderHttpClientInterface
{
    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @param HttpClient|null $client
     */
    public function __construct(HttpClient $client, RequestFactoryInterface $requestFactory)
    {
        $this->client         = $client;
        $this->requestFactory = $requestFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function get($uri) : ResponseInterface
    {
        $request = $this->requestFactory->createRequest('GET', $uri)
            ->withHeader('User-Agent', 'HTTPie/0.9.2');
        return new Psr7ResponseDecorator($this->client->sendRequest($request));
    }
}
