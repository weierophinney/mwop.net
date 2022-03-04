<?php

declare(strict_types=1);

namespace Mwop\App\Middleware;

use Laminas\Diactoros\Response\Serializer;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class CacheMiddleware implements MiddlewareInterface
{
    public function __construct(
        private CacheItemPoolInterface $cache,
        private LoggerInterface $logger,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        $key  = substr(hash('sha256', $path), 0, 16);

        try {
            $item = $this->cache->getItem($key);
        } catch (Throwable $e) {
            $this->logger->warning(sprintf(
                'Error retrieving or creating cache item %s: %s',
                $path,
                $e->getMessage(),
            ));

            return $handler->handle($request);
        }

        if (! $item->isHit()) {
            $this->logger->info(sprintf('No hit for %s', $path));
            return $this->cacheFromHandler($item, $request, $handler);
        }

        try {
            $this->logger->info(sprintf('Attempting to return cached response for %s', $path));
            return Serializer::fromString($item->get());
        } catch (Throwable $e) {
            $this->logger->warning(sprintf(
                'Error deserializing cached response for %s: %s',
                $path,
                $e->getMessage(),
            ));
        }

        return $this->cacheFromHandler($item, $request, $handler);
    }

    private function cacheFromHandler(
        CacheItemInterface $cacheItem,
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        $response = $handler->handle($request);

        try {
            $cacheItem->set(Serializer::toString($response));
            $this->cache->save($cacheItem);
        } catch (Throwable $e) {
            $this->logger->warning(sprintf(
                'Error caching response for %s: %s',
                $request->getUri()->getPath(),
                $e->getMessage(),
            ));
        }

        $this->logger->info(sprintf('Cached response for %s', $request->getUri()->getPath()));
        return $response;
    }
}
