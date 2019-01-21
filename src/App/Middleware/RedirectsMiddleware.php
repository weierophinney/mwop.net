<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\UriInterface as Uri;
use Zend\Diactoros\Response\RedirectResponse;

class RedirectsMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandlerInterface $handler) : Response
    {
        $url  = $request->getUri();
        $path = $url->getPath();

        // Ensure php.net is able to retrieve PHP RSS feed without a problem
        if ('/blog/tag/php.xml' === $path) {
            return $handler->handle($request);
        }

        // PhlyBlog style pagination
        if (preg_match('#^/blog-p(?P<page>\d+)\.html$#', $path, $matches)) {
            return $this->redirect('/blog', $url, ['page' => $matches['page']]);
        }
        if (preg_match('#^/blog/tag/(?P<tag>.*?)-p(?P<page>\d+)\.html$#', $path, $matches)) {
            return $this->redirect(sprintf('/blog/tag/%s', $matches['tag']), $url, ['page' => $matches['page']]);
        }
        if (preg_match('#^/blog\.html(?P<path>/.*)?$#', $path, $matches)) {
            $blogPath = isset($matches['path']) ? $matches['path'] : '';
            return $this->redirect('/blog' . $blogPath, $url);
        }

        // PhlyBlog style feed URIs
        if (preg_match('#^/blog/tag/(?P<tag>.*?)-(?P<type>atom|rss)\.xml#', $path, $matches)) {
            return $this->redirect(sprintf('/blog/tag/%s/%s.xml', $matches['tag'], $matches['type']), $url);
        }

        // Serendipity style feed URIs
        if (preg_match('#^/blog/tag/(?P<tag>[^/.]+)(?!-(atom|rss))\.xml#', $path, $matches)) {
            return $this->redirect(sprintf('/blog/tag/%s/rss.xml', $matches['tag']), $url);
        }

        // Problematic posts due to bad characters
        if (preg_match('#^/blog/.*?[,;!].*\.html#', $path)) {
            $newPath = str_replace([',', ';', '!'], '', $path);
            $newPath = preg_replace('#\.+\.html#', '.html', $newPath);
            $newPath = strtolower($newPath);
            return $this->redirect($newPath, $url);
        }

        // Redirect blog posts not ending in .html to .html version
        if (preg_match('#^/blog/(?<!tag/)(?P<post>[^/]+)$#', $path, $matches)
            && ! preg_match('#\.(html|xml)$#', $path)
        ) {
            $newPath = sprintf('/blog/%s.html', $matches['post']);
            return $this->redirect($newPath, $url);
        }

        // Former uploads
        if (preg_match('#^/uploads/#', $path)) {
            $uri = $url
                ->withHost('uploads.mwop.net')
                ->withScheme('https');
            return $this->redirect(substr($path, 8), $uri);
        }

        // Former screencasts
        if (preg_match('#^/screencasts/#', $path)) {
            $uri = $url
                ->withHost('screencasts.mwop.net')
                ->withScheme('https');
            return $this->redirect(substr($path, 12), $uri);
        }

        // Former slides
        if (preg_match('#^/slides/#', $path)) {
            $uri = $url
                ->withHost('slides.mwop.net')
                ->withScheme('https');
            return $this->redirect(substr($path, 7), $uri);
        }

        // Serendipity
        if (preg_match('#^/matthew#', $path)) {
            $regexes = [
                '^/matthew/feeds/index.rss2'                          => '/blog/rss.xml',
                '^/matthew/feeds/atom.xml'                            => '/blog/atom.xml',
                '^/matthew/archives/(\d{4}).html'                     => '/blog', // no longer supporting by year
                '^/matthew/archives/(\d{4})/(\d{2}).html'             => '/blog', // no longer supporting by month
                '^/matthew/archives/(\d{4})/(\d{2})/(\d{2}).html'     => '/blog', // no longer supporting by day
                '^/matthew/archives/([^/]+).html'                     => '/blog/$1.html',
                '^/matthew/plugin/tag/([^/]+)'                        => '/blog/tag/$1',
                '^/matthew/categories/\d+-([^/]+).rss'                => '/blog/tag/$1/rss.xml',
                '^/matthew/categories/\d+-([^/]+)'                    => '/blog/tag/$1',
            ];
            foreach ($regexes as $regex => $replacement) {
                $regex = '#' . $regex . '#';
                if (preg_match($regex, $path)) {
                    $path = preg_replace($regex, $replacement, $path);
                    return $this->redirect($path, $url);
                }
            }
            if (preg_match('#^/matthew/rss\.php$#', $path)) {
                if (! isset($request->getQueryParams()['serendipity']['tag'])) {
                    return $this->redirect('/blog/rss.xml', $url);
                }
                return $this->redirect(sprintf(
                    '/blog/tag/%s/rss.xml',
                    $request->getQueryParams()['serendipity']['tag']
                ), $url);
            }
            return $this->redirect('/blog', $url);
        }

        return $handler->handle($request);
    }

    private function redirect(string $path, Uri $url, array $query = []) : RedirectResponse
    {
        $url = $url->withPath($path);

        if (count($query)) {
            $url = $url->withQuery(http_build_query($query));
        }

        return new RedirectResponse((string) $url, 301);
    }
}
