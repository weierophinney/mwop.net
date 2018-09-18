<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Swoole\Event;
use Throwable;
use Zend\Diactoros\Response\Serializer;

class CachingMiddleware implements MiddlewareInterface
{
    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var bool
     */
    private $enabled;

    public function __construct(CacheItemPoolInterface $cache, bool $enabled = true)
    {
        $this->cache   = $cache;
        $this->enabled = $enabled;
    }

    /**
     * @return Response
     */
    public function process(Request $request, RequestHandlerInterface $handler) : Response
    {
        $id = $request->getAttribute('id', false);

        if (! empty($request->getQueryParams()['amp'])) {
            $id .= '-amp';
        }

        // Caching is disabled, or no identifier present; invoke the middleware.
        if (! $this->enabled || ! $id) {
            return $handler->handle($request);
        }

        $item = $this->cache->getItem($id);
        $response = $this->unserializeResponse($item);
        if ($response instanceof Response) {
            // Hit cache, and was able to unserialize the data to a response.
            return $response;
        }

        // Invoke middleware
        $response = $handler->handle($request);

        // Result represents an error response; cannot cache.
        if (300 <= $response->getStatusCode()) {
            return $response;
        }

        // Cache result
        Event::defer(function () use ($item, $response) {
            $item->set(Serializer::toString($response));
            $this->cache->save($item);
        });

        return $response;
    }

    private function unserializeResponse(CacheItemInterface $item) : ?Response
    {
        if (! $item->isHit()) {
            return null;
        }

        $data = $item->get();
        
        try {
            $response = Serializer::fromString($data);
            return $response;
        } catch (Throwable $e) {
            return null;
        }
    }
}
