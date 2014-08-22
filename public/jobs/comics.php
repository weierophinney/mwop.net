<?php
chdir(__DIR__ . '/../../');

if ($_SERVER['REMOTE_ADDR'] !== $_SERVER['SERVER_ADDR']) {
    header('HTTP/1.1 403 Forbidden');
    exit(1);
};

$command = '/usr/local/zend/bin/php -d date.timezone=America/Chicago vendor/phly/phly-comic/bin/phly-comic.php fetch-all';
exec($command, $output, $return);
if ($return != 0) {
    ZendJobQueue::setCurrentJobStatus(ZendJobQueue::FAILED);
    exit(1);
}
ZendJobQueue::setCurrentJobStatus(ZendJobQueue::OK);
exit(0);
