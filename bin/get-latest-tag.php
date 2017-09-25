#!/usr/bin/env php
<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

const URL_TEMPLATE = 'https://registry.hub.docker.com/v2/repositories/%s/%s/tags/';

$user = $argv[1];
$repo = $argv[2];

$url = sprintf(URL_TEMPLATE, $user, $repo);
$json = file_get_contents($url);
$data = json_decode($json);

if (! isset($data->results) || 0 === count($data->results)) {
    fwrite(STDERR, sprintf("No tags found for %s/%s%s", $user, $repo, PHP_EOL));
    exit(1);
}

$mostRecent = null;
$lastUpdated = null;
foreach ($data->results as $result) {
    if (null === $mostRecent) {
        $mostRecent = $result->name;
        $lastUpdated = new DateTime($result->last_updated);
        continue;
    }

    $test = new DateTime($result->last_updated);
    if ($test > $lastUpdated) {
        $mostRecent = $result->name;
        $lastUpdated = $test;
        continue;
    }
}

fwrite(STDOUT, $mostRecent);
exit(0);
