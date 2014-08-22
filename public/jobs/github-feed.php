<?php
chdir(__DIR__ . '/../../');

if ($_SERVER['REMOTE_ADDR'] !== $_SERVER['SERVER_ADDR']) {
    header('HTTP/1.1 403 Forbidden');
    exit(1);
};

$command = '/usr/local/zend/bin/php -d date.timezone=America/Chicago public/index.php githubfeed fetch';
exec($command, $output, $return);
if ($return != 0) {
    ZendJobQueue::setCurrentJobStatus(ZendJobQueue::FAILED);
    exit(1);
}
ZendJobQueue::setCurrentJobStatus(ZendJobQueue::OK);
exit(0);
