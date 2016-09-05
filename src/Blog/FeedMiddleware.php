<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Diactoros\Stream;

class FeedMiddleware
{
    private $feedPath;

    public function __construct(string $feedPath = 'data/feeds')
    {
        $this->feedPath = $feedPath;
    }

    public function __invoke(Request $req, Response $res, callable $next) : Response
    {
        $tag  = $req->getAttribute('tag');
        $type = $req->getAttribute('type', 'rss');
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

    private function getTagFeedPath(string $tag, string $type) : string
    {
        return sprintf(
            '%s/%s.%s.xml',
            $this->feedPath,
            str_replace([' ', '%20'], '+', $tag),
            $type
        );
    }

    private function getFeedPath(string $type) : string
    {
        return sprintf('%s/%s.xml', $this->feedPath, $type);
    }
}
