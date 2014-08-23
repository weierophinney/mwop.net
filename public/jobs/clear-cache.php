<?php
chdir(__DIR__ . '/../../');

if (! ZendJobQueue::getCurrentJobId()) {
    header('HTTP/1.1 403 Forbidden');
    exit(1);
}

$hosts = ['mwop.net', 'www.mwop.net', 'staging.mwop.net'];
$rules = [
    'mwop_home'   => '/',
    'mwop_resume' => '/resume',
];

foreach ($hosts as $host) {
    $baseUrl = 'http://' . $host;

    foreach ($rules as $rule => $path) {
        page_cache_remove_cached_contents_by_uri(
            $rule,
            $baseUrl . $path
        );
    }
}

ZendJobQueue::setCurrentJobStatus(ZendJobQueue::OK);
exit(0);
