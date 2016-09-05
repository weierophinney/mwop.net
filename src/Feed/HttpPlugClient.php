<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Feed;

use Http\Client\HttpClient;
use Zend\Diactoros\Request;
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
     * @param HttpClient|null $client
     */
    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function get($uri) : ResponseInterface
    {
        $request = (new Request($uri, 'GET'))
            ->withHeader('User-Agent', 'HTTPie/0.9.2');
        return new Psr7ResponseDecorator($this->client->sendRequest($request));
    }
}
