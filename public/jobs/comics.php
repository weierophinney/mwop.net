<?php
chdir(__DIR__ . '/../../');

if (! ZendJobQueue::getCurrentJobId()) {
    header('HTTP/1.1 403 Forbidden');
    exit(1);
}

$command = '/usr/local/zend/bin/php -d date.timezone=America/Chicago vendor/phly/phly-comic/bin/phly-comic.php fetch-all';
exec($command, $output, $return);
if ($return != 0) {
    ZendJobQueue::setCurrentJobStatus(ZendJobQueue::FAILED);
    exit(1);
}

header('Content-Type: application/json');
echo json_encode([
    'status' => $return,
    'output' => explode("\n", $output),
]);

// Clear caches
$queue  = new ZendJobQueue();
$queue->createHttpJob('/jobs/clear-cache.php', [], [
    'name'       => 'clear-cache',
    'persistent' => false,
]);

ZendJobQueue::setCurrentJobStatus(ZendJobQueue::OK);
exit(0);
