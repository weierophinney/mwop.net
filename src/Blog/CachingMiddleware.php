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
            return $next();
        }

        if (! $req->blog) {
            $req->blog = (object) [
                'from_cache' => false,
                'cacheable'  => false,
            ];
        }

        if (! $res->isComplete() && ! $req->blog->from_cache) {
            return $this->fetchFromCache($req, $res, $next);
        }

        if ($res->isComplete()
            && ! $req->blog->from_cache
            && $req->blog->cacheable
        ) {
            return $this->cache($req, $res, $next);
        }

        return $next();
    }

    private function fetchFromCache($req, $res, $next)
    {
        $path = parse_url($req->getUrl(), PHP_URL_PATH);
        if (! preg_match('#^/(?P<page>[^/]+\.html)$#', $path, $matches)) {
            // Nothing to do; not a blog post
            return $next();
        }

        $cachePath = sprintf('%s/%s', $this->cachePath, $matches['page']);

        if (! file_exists($cachePath)) {
            // Nothing in cache, but should be cached
            $req->blog->cacheable = $matches['page'];
            return $next();
        }

        // Cache hit!
        $res->setBody(new Stream(fopen($cachePath, 'r')));
        $res->end();
        $req->blog->from_cache = true;
    }

    private function cache($req, $res, $next)
    {
        $cachePath = sprintf('%s/%s', $this->cachePath, $req->blog->cacheable);
        file_put_contents($cachePath, (string) $res->getBody());
    }
}
