<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use RuntimeException;
use Throwable;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Stream;

class CachingMiddleware implements MiddlewareInterface
{
    /**
     * @var string
     */
    private $cachePath;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var MiddlewareInterface
     */
    private $middleware;

    public function __construct(MiddlewareInterface $middleware, string $cachePath, bool $enabled = true)
    {
        $this->middleware = $middleware;
        $this->cachePath  = $cachePath;
        $this->enabled    = $enabled;
    }

    /**
     * @return Response
     */
    public function process(Request $request, DelegateInterface $delegate)
    {
        $middleware = $this->middleware;
        $id         = $request->getAttribute('id', false);

        if (! empty($request->getQueryParams()['amp'])) {
            $id .= '-amp';
        }

        // Caching is disabled, or no identifier present; invoke the middleware.
        if (! $this->enabled || ! $id) {
            return $middleware->process($request, $delegate);
        }

        $result = $this->fetchFromCache($id);

        // Hit cache; resturn response.
        if ($result instanceof Response) {
            return $result;
        }

        // Invoke middleware
        $result = $middleware->process($request, $delegate);

        // Result is not a response; cannot cache; error condition.
        if (! $result instanceof Response) {
            $error = $this->prepareExceptionFromMiddlewareResult($result);
            throw $error;
        }

        // Result represents an error response; cannot cache.
        if (300 <= $result->getStatusCode()) {
            return $result;
        }

        // Cache result
        $this->cache($id, $result);

        return $result;
    }

    /**
     * @return false|HtmlResponse
     */
    private function fetchFromCache(string $id)
    {
        $cachePath = sprintf('%s/%s', $this->cachePath, $id);

        if (! file_exists($cachePath)) {
            // Nothing in cache, but should be cached
            return false;
        }

        // Cache hit!
        return (new HtmlResponse())
            ->withBody(new Stream(fopen($cachePath, 'r')));
    }

    private function cache(string $id, Response $res)
    {
        $cachePath = sprintf('%s/%s', $this->cachePath, $id);
        file_put_contents($cachePath, (string) $res->getBody());
    }

    private function prepareExceptionFromMiddlewareResult($result) : Throwable
    {
        if ($result instanceof Throwable) {
            return $result;
        }

        if (is_string($result)) {
            return new RuntimeException($result);
        }

        if (is_scalar($result) || is_array($result)) {
            return new RuntimeException(var_export($result, true));
        }

        return new RuntimeException((string) $result);
    }
}
