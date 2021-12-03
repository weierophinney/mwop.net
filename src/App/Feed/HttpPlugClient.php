<?php // phpcs:disable Generic.WhiteSpace.ScopeIndent.IncorrectExact


declare(strict_types=1);

namespace Mwop\App\Feed;

use Http\Client\HttpClient;
use Laminas\Feed\Reader\Http\ClientInterface as FeedReaderHttpClientInterface;
use Laminas\Feed\Reader\Http\Psr7ResponseDecorator;
use Laminas\Feed\Reader\Http\ResponseInterface;
use Psr\Http\Message\RequestFactoryInterface;

class HttpPlugClient implements FeedReaderHttpClientInterface
{
    public function __construct(
        private HttpClient $client,
        private RequestFactoryInterface $requestFactory
    ) {
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
