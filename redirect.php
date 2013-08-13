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
