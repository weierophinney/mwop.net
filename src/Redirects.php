<?php
namespace Mwop;

use Phly\Http\Uri;

class Redirects
{
    public function __invoke($req, $res, $next)
    {
        $url  = $req->getUrl();
        $path = $url->path;

        // Ensure php.net is able to retrieve PHP RSS feed without a problem.
        if ('/blog/tag/php.xml' === $path) {
            return $next();
        }

        // PhlyBlog style pagination
        if (preg_match('#^/blog-p(?P<page>\d+)\.html$#', $path, $matches)) {
            return $this->redirect('/blog', $url, $res, ['page' => $matches['page']]);
        }
        if (preg_match('#^/blog/tag/(?P<tag>.*?)-p(?P<page>\d+)\.html$#', $path, $matches)) {
            return $this->redirect(sprintf('/blog/tag/%s', $matches['tag']), $url, $res, ['page' => $matches['page']]);
        }

        // PhlyBlog style feed URIs
        if (preg_match('#^/blog/tag/(?P<tag>.*?)-(?P<type>atom|rss)\.xml#', $path, $matches)) {
            return $this->redirect(sprintf('/blog/tag/%s/%s.xml', $matches['tag'], $matches['type']), $url, $res);
        }

        // Serendipity style feed URIs
        if (preg_match('#^/blog/tag/(?P<tag>[^/.]+)(?!-(atom|rss))\.xml#', $path, $matches)) {
            return $this->redirect(sprintf('/blog/tag/%s/rss.xml', $matches['tag']), $url, $res);
        }

        // Former uploads
        if (preg_match('#^/uploads/#', $path)) {
            return $this->redirect(sprintf('http://uploads.mwop.net/%s', substr($path, 9)), $url, $res);
        }

        // Former screencasts
        if (preg_match('#^/screencasts/#', $path)) {
            return $this->redirect(sprintf('http://screencasts.mwop.net/%s', substr($path, 13)), $url, $res);
        }

        // Former slides
        if (preg_match('#^/slides/#', $path)) {
            return $this->redirect(sprintf('http://slides.mwop.net/%s', substr($path, 8)), $url, $res);
        }

        // Serendipity
        if (preg_match('#^/matthew#', $path)) {
            $regexes = array(
                '^/matthew/feeds/index.rss2'                          => '/blog/rss.xml',
                '^/matthew/feeds/atom.xml'                            => '/blog/atom.xml',
                '^/matthew/archives/(\d{4}).html'                     => '/blog', // no longer supporting by year
                '^/matthew/archives/(\d{4})/(\d{2}).html'             => '/blog', // no longer supporting by month
                '^/matthew/archives/(\d{4})/(\d{2})/(\d{2}).html'     => '/blog', // no longer supporting by day
                '^/matthew/archives/([^/]+).html'                     => '/blog/$1.html',
                '^/matthew/plugin/tag/([^/]+)'                        => '/blog/tag/$1',
                '^/matthew/categories/\d+-([^/]+).rss'                => '/blog/tag/$1/rss.xml',
                '^/matthew/categories/\d+-([^/]+)'                    => '/blog/tag/$1',
                '^/matthew/rss\.php\?.*serendipity\[tag\]\=([^&=]+)$' => '/blog/tag/$1/rss.xml',
            );
            foreach ($regexes as $regex => $replacement) {
                $regex = '#' . $regex . '#';
                if (preg_match($regex, $path)) {
                    $path = preg_replace($regex, $replacement, $path);
                    return $this->redirect($path, $url, $res);
                }
            }
            if (preg_match('#^/matthew/rss\.php$#', $path)) {
                if (! isset($req->query['serendipity']['tag'])) {
                    return $this->redirect('/blog', $url, $res);
                }
                return $this->redirect(sprintf(
                    '/blog/tag/%s/rss.xml',
                    $req->query['serendipity']['tag']
                ), $url, $res);
            }
            return $this->redirect('/blog', $url, $res);
        }

        $next();
    }

    private function redirect($path, $url, $res, $query = [])
    {
        $urlParams = [
            'scheme' => $url->scheme,
            'host'   => $url->host,
            'port'   => $url->port,
            'path'   => $path,
        ];

        if (count($query)) {
            $urlParams['query'] = http_build_query($query);
        }

        $url = Uri::fromArray($urlParams);
        $res->setStatusCode(301);
        $res->addHeader('Location', (string) $url);
        $res->end();
    }
}
