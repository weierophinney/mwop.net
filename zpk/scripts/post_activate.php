<?php
$queue = new ZendJobQueue();
$queue->createHttpJob('/jobs/comics.php', [], [
    'schedule' => '0 5 * * *', // every day, at 5 AM
]);
$queue->createHttpJob('/jobs/github-feed.php', [], [
    'schedule' => '5,20,35,40 * * * *', // every 15 minutes
]);
