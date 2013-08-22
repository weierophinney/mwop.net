<?php
if (!isset($_SERVER['REQUEST_URI'])) {
    return;
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (preg_match('#^/slides/#', $uri)) {
    $newUri = sprintf('http://slides.mwop.net/%s', substr($uri, 8));
    header(sprintf('Location: %s', $newUri), true, 301);
    exit(0);
}

if (preg_match('#^/matthew#', $uri)) {
    $regexes = array(
        '^/matthew/feeds/index.rss2'                          => '/blog-rss.xml',
        '^/matthew/feeds/atom.xml'                            => '/blog-atom.xml',
        '^/matthew/archives/(\d{4}).html'                     => '/blog/year/$1.html',
        '^/matthew/archives/(\d{4})/(\d{2}).html'             => '/blog/month/$1/$2.html',
        '^/matthew/archives/(\d{4})/(\d{2})/(\d{2}).html'     => '/blog/day/$1/$2/$3.html',
        '^/matthew/archives/([^/]+).html'                     => '/blog/$1.html',
        '^/matthew/plugin/tag/([^/]+)'                        => '/blog/tag/$1.html',
        '^/matthew/categories/\d+-([^/]+).rss'                => '/blog/tag/$1-rss.xml',
        '^/matthew/categories/\d+-([^/]+)'                    => '/blog/tag/$1.html',
        '^/matthew/rss\.php\?.*serendipity\[tag\]\=([^&=]+)$' => '/blog/tag/$1-rss.xml',
    );
    foreach ($regexes as $regex => $replacement) {
        if (preg_match($regex, $uri)) {
            $newUri = preg_replace($regex, $replacement, $uri);
            header(sprintf('Location: %s', $newUri), true, 301);
            exit(0);
        }
    }
    header('Location: http://mwop.net/blog.html', true, 301);
    exit(0);
}
