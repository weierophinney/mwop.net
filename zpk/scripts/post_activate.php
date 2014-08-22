<?php
$queue = new ZendJobQueue();
$queue->createHttpJob('http://staging.mwop.net/jobs/comics.php', [], [
    'schedule' => '0 5 * * *', // every day, at 5 AM
]);
$queue->createHttpJob('http://staging.mwop.net/jobs/github-feed.php', [], [
    'schedule' => '5,20,35,40 * * * *', // every 15 minutes
]);
