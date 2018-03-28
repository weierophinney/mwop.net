<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

class FeedMiddleware implements MiddlewareInterface
{
    private $feedPath;

    public function __construct(string $feedPath = 'data/feeds')
    {
        $this->feedPath = $feedPath;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $tag  = $request->getAttribute('tag');
        $type = $request->getAttribute('type', 'rss');
        $path = $tag
            ? $this->getTagFeedPath($tag, $type)
            : $this->getFeedPath($type);

        if (! file_exists($path)) {
            return $handler->handle($request);
        }

        return (new Response())
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
