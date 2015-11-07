<?php
namespace Mwop\Blog\Console;

use Mwop\Blog;
use Zend\Stratigility\Http\Request;
use Zend\Diactoros\ServerRequest as PsrRequest;
use Zend\Diactoros\Response;
use Zend\Diactoros\Uri;
use Zend\Console\ColorInterface as Color;

class CachePosts
{
    private $blogMiddleware;

    public function __construct(callable $blog)
    {
        $this->blogMiddleware = $blog;
    }

    public function __invoke($route, $console)
    {
        $basePath = $route->getMatchedParam('path');

        $path       = realpath($basePath) . '/data/posts';
        $baseUri    = new Uri('https://mwop.net/blog');
        $middleware = $this->blogMiddleware;

        $console->writeLine('Generating static cache for blog posts', Color::GREEN);

        // Prepare final handler for middleware
        $failed = false;
        $done = function ($req, $res, $err = null) use (&$failed) {
            $failed = ($err) ? true : false;
        };

        foreach (new Blog\PhpFileFilter($path) as $fileInfo) {
            $entry  = include $fileInfo->getPathname();

            if (! $entry instanceof Blog\EntryEntity) {
                continue;
            }

            $message = '    ' . $entry->getId();
            $length  = strlen($message);
            $width   = $console->getWidth();
            $console->write($message, Color::BLUE);

            $canonical = $baseUri->withPath(sprintf('/blog/%s.html', $entry->getId()));
            $request   = (new Request(new PsrRequest([], [], $canonical, 'GET')))
                ->withUri($canonical)
                ->withAttribute('id', $entry->getId());

            $failed = false;
            $middleware($request, new Response(), $done);

            $this->reportComplete($console, $width, $length, ! $failed);
        }

        $console->writeLine('ALL DONE', Color::GREEN);
        return 0;
    }

    /**
     * Report success
     *
     * @param \Zend\Console\Adapter\AdapterInterface $console
     * @param int $width
     * @param int $length
     * @param bool $success
     * @return int
     */
    private function reportComplete($console, $width, $length, $success = true)
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
}
