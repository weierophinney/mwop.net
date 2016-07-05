<?php
namespace Mwop\Feed;

use Http\Client\HttpClient;
use Zend\Diactoros\Request;
use Zend\Feed\Reader\Http\ClientInterface as FeedReaderHttpClientInterface;
use Zend\Feed\Reader\Http\Psr7ResponseDecorator;

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
    public function get($uri)
    {
        return new Psr7ResponseDecorator(
            $this->client->sendRequest(new Request($uri, 'GET'))
        );
    }
}
