<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Diactoros\Stream;

class CachingMiddleware
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
     * @var callable
     */
    private $middleware;

    public function __construct(callable $middleware, string $cachePath, bool $enabled = true)
    {
        $this->middleware = $middleware;
        $this->cachePath  = $cachePath;
        $this->enabled    = $enabled;
    }

    public function __invoke(Request $req, Response $res, callable $next) : Response
    {
        $middleware = $this->middleware;
        $id         = $req->getAttribute('id', false);

        // Caching is disabled, or no identifier present; invoke the middleware.
        if (! $this->enabled || ! $id) {
            return $middleware($req, $res, $next);
        }

        $result = $this->fetchFromCache($id, $res);

        // Hit cache; resturn response.
        if ($result instanceof ResponseInterface) {
            return $result;
        }

        // Invoke middleware
        $result = $middleware($req, $res, $next);

        // Result is not a response; cannot cache; error condition.
        if (! $result instanceof ResponseInterface) {
            return $next($req, $res, $result);
        }

        // Result represents an error response; cannot cache.
        if (300 <= $result->getStatusCode()) {
            return $result;
        }

        // Cache result
        $this->cache($id, $result);

        return $result;
    }

    private function fetchFromCache(string $id, Response $res)
    {
        $cachePath = sprintf('%s/%s', $this->cachePath, $id);

        if (! file_exists($cachePath)) {
            // Nothing in cache, but should be cached
            return false;
        }

        // Cache hit!
        return $res->withBody(new Stream(fopen($cachePath, 'r')));
    }

    private function cache(string $id, Response $res)
    {
        $cachePath = sprintf('%s/%s', $this->cachePath, $id);
        file_put_contents($cachePath, (string) $res->getBody());
    }
}
