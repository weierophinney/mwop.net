<?php
namespace Mwop\Blog;

use Phly\Http\Stream;

class CachingMiddleware
{
    private $cachePath;

    private $enabled;

    public function __construct($cachePath, $enabled = true)
    {
        $this->cachePath = $cachePath;
        $this->enabled   = $enabled;
    }

    public function __invoke($req, $res, $next)
    {
        if (! $this->enabled) {
            return $next($req, $res);
        }

        if (! $req->getAttribute('blog', false)) {
            $req = $req->withAttribute('blog', (object) [
                'from_cache' => false,
                'cacheable'  => false,
            ]);
        }

        if (! $res->isComplete() && ! $req->getAttribute('blog')->from_cache) {
            return $this->fetchFromCache($req, $res, $next);
        }

        if ($res->isComplete()
            && ! $req->getAttribute('blog')->from_cache
            && $req->getAttribute('blog')->cacheable
        ) {
            return $this->cache($req, $res, $next);
        }

        return $next($req, $res);
    }

    private function fetchFromCache($req, $res, $next)
    {
        $path = $req->getUri()->getPath();
        if (! preg_match('#^/(?P<page>[^/]+\.html)$#', $path, $matches)) {
            // Nothing to do; not a blog post
            return $next($req, $res);
        }

        $cachePath = sprintf('%s/%s', $this->cachePath, $matches['page']);

        if (! file_exists($cachePath)) {
            // Nothing in cache, but should be cached
            $req->getAttribute('blog')->cacheable = $matches['page'];
            return $next($req, $res);
        }

        // Cache hit!
        $req->getAttribute('blog')->from_cache = true;
        return $res->withBody(new Stream(fopen($cachePath, 'r')));
    }

    private function cache($req, $res, $next)
    {
        $cachePath = sprintf('%s/%s', $this->cachePath, $req->getAttribute('blog')->cacheable);
        file_put_contents($cachePath, (string) $res->getBody());
    }
}
