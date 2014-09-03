<?php
$server = 'https://mwop.net';
chdir(__DIR__ . '/../../');

if (! ZendJobQueue::getCurrentJobId()) {
    header('HTTP/1.1 403 Forbidden');
    exit(1);
}

$command = '/usr/local/zend/bin/php -d date.timezone=America/Chicago vendor/phly/phly-comic/bin/phly-comic.php fetch-all';
exec($command, $output, $return);
if ($return != 0) {
    ZendJobQueue::setCurrentJobStatus(ZendJobQueue::FAILED);
    header('Content-Type: text/plain');
    echo implode("\n", $output);
    exit(1);
}

ZendJobQueue::setCurrentJobStatus(ZendJobQueue::OK);
header('Content-Type: text/plain');
echo implode("\n", $output);

// Clear caches
$queue  = new ZendJobQueue();
$queue->createHttpJob($server . '/jobs/clear-cache.php', [], [
    'name'       => 'clear-cache',
    'persistent' => false,
]);

exit(0);
