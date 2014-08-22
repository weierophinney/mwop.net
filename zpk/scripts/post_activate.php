<?php
$server = 'http://staging.mwop.net';
$queue  = new ZendJobQueue();

// First, remove any existing job schedules for our application
foreach ($queue->getSchedulingRules() as $job) {
    $script = $job['script'];
    if (0 !== strpos($script, $server)) {
        // Job is not one we're interested in
        continue;
    }

    // Remove a previously scheduled job
    $queue->deleteSchedulingRule($job['id']);
}

// Add scheduled job for fetching comics
$queue->createHttpJob('http://staging.mwop.net/jobs/comics.php', [], [
    'schedule' => '0 10 * * *', // every day, at 5 AM America/Chicago (server is in UTC)
]);

// Add scheduled job for fetching github feed
$queue->createHttpJob('http://staging.mwop.net/jobs/github-feed.php', [], [
    'schedule' => '5,20,35,40 * * * *', // every 15 minutes
]);
