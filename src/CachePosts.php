<?php
namespace Mwop;

use Phly\Conduit\Http\Request;
use Phly\Http\Request as PsrRequest;
use Phly\Http\Response;
use Phly\Http\Uri;
use Zend\Console\ColorInterface as Color;

class CachePosts
{
    private $blogMiddleware;

    public function __construct(Blog\Middleware $blog)
    {
        $this->blogMiddleware = $blog;
    }

    public function __invoke($route, $console)
    {
        $basePath = $route->getMatchedParam('path');

        $path = realpath($basePath) . '/data/posts';
        $uri  = Uri::fromArray([
            'scheme' => 'https',
            'host'   => 'mwop.net',
            'path'   => '/blog',
        ]);
        $middleware = $this->blogMiddleware;

        $request  = new Request(new PsrRequest);
        $request->setMethod('GET');
        
        $console->writeLine('Generating static cache for blog posts', Color::GREEN);

        foreach (new Blog\PhpFileFilter($path) as $fileInfo) {
            $entry  = include $fileInfo->getPathname();

            if (! $entry instanceof Blog\EntryEntity) {
                continue;
            }

            $message = '    ' . $entry->getId();
            $length  = strlen($message);
            $width   = $console->getWidth();
            $console->write($message, Color::BLUE);

            $canonical = $uri->setPath(sprintf('/blog/%s.html', $entry->getId()));
            $request->originalUrl = $canonical;

            $uri = $uri->setPath(sprintf('/%s.html', $entry->getId()));
            $request->setUrl($uri);

            $failed = false;
            $done = function ($err = null) use (&$failed) {
                $failed = ($err) ? true : false;
            };

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
