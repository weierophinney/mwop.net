<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog\Console;

use Psr\Http\Server\MiddlewareInterface;
use Mni\FrontYAML\Bridge\CommonMark\CommonMarkParser;
use Mni\FrontYAML\Parser;
use Mwop\Blog\MarkdownFileFilter;
use Psr\Http\Message\StreamInterface;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Console\ColorInterface as Color;
use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;
use Zend\Diactoros\Uri;
use Zend\Expressive\Handler\NotFoundHandler;
use ZF\Console\Route;

class CachePosts
{
    private $defaultHandler;

    private $blogMiddleware;

    public function __construct(MiddlewareInterface $blog, NotFoundHandler $defaultHandler)
    {
        $this->blogMiddleware = $blog;
        $this->defaultHandler = $defaultHandler;
    }

    public function __invoke(Route $route, Console $console) : int
    {
        $basePath = $route->getMatchedParam('path');

        $path       = realpath($basePath) . '/data/blog';
        $cache      = realpath($basePath) . '/data/cache/posts';
        $baseUri    = new Uri('https://swoole.mwop.net');
        $middleware = $this->blogMiddleware;

        $console->writeLine('Generating static cache for blog posts', Color::GREEN);

        $parser = new Parser(null, new CommonMarkParser());
        foreach (new MarkdownFileFilter($path) as $fileInfo) {
            $document = $parser->parse(file_get_contents($fileInfo->getPathname()));
            $metadata = $document->getYAML();

            $message = '    ' . $metadata['id'];
            $length  = strlen($message);
            $width   = $console->getWidth();
            $console->write($message, Color::BLUE);

            $canonical = $baseUri->withPath(sprintf('/blog/%s.html', $metadata['id']));
            $request   = (new Request([], [], $canonical, 'GET'))
                ->withUri($canonical)
                ->withAttribute('id', $metadata['id']);

            $response = $middleware->process($request, $this->defaultHandler);

            $failed = (200 !== $response->getStatusCode());

            if (! $failed) {
                $this->cacheResponse($metadata['id'], $cache, $response->getBody());
            }

            $this->reportComplete($console, $width, $length, ! $failed);
        }

        $console->writeLine('ALL DONE', Color::GREEN);
        return 0;
    }

    /**
     * Report success
     */
    private function reportComplete(Console $console, int $width, int $length, bool $success = true)
    {
        if (($length + 8) > $width) {
            $console->writeLine('');
            $length = 0;
        }
        $message = $success ? '[ DONE ]' : '[ FAIL ]';
        $spaces  = $width - $length - 8;
        $spaces  = ($spaces > 0) ? $spaces : 0;
        $color   = $success ? Color::GREEN : Color::RED;
        $console->writeLine(str_repeat('.', $spaces) . $message, $color);
    }

    /**
     * Cache the response content.
     *
     * @param string $id Post identifier.
     * @param string $cache Path to cache files.
     * @param StreamInterface $content Content to cache.
     */
    private function cacheResponse(string $id, string $cache, StreamInterface $content)
    {
        $filename = sprintf('%s/%s', $cache, $id);
        file_put_contents($filename, (string) $content);
    }
}
