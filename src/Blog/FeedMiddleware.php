<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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

    /**
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $tag  = $request->getAttribute('tag');
        $type = $request->getAttribute('type', 'rss');
        $path = $tag
            ? $this->getTagFeedPath($tag, $type)
            : $this->getFeedPath($type);

        if (! file_exists($path)) {
            return $delegate->process($request);
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
