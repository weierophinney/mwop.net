<?php
namespace Mwop\Blog;

use Zend\Diactoros\Stream;

class FeedMiddleware
{
    private $feedPath;

    public function __construct($feedPath = 'data/feeds')
    {
        $this->feedPath = $feedPath;
    }

    public function __invoke($req, $res, $next)
    {
        $tag  = $req->getAttribute('tag', false);
        $type = $req->getAttribute('type');
        $path = $tag
            ? $this->getTagFeedPath($tag, $type)
            : $this->getFeedPath($type);

        if (! file_exists($path)) {
            return $next($req, $res->withStatus(404), 'Not found');
        }

        return $res
            ->withHeader('Content-Type', sprintf('application/%s+xml', $type))
            ->withBody(new Stream(fopen($path, 'r')));
    }

    private function getTagFeedPath($tag, $type)
    {
        return sprintf(
            '%s/%s.%s.xml',
            $this->feedPath,
            str_replace([' ', '%20'], '+', $tag),
            $type
        );
    }

    private function getFeedPath($type)
    {
        return sprintf('%s/%s.xml', $this->feedPath, $type);
    }
}
