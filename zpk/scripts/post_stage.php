<?php
$baseDir = getenv('ZS_APPLICATION_BASE_DIR');
if (!chdir($baseDir)) {
    throw new Exception(sprintf(
        'Unable to change directory to %s',
        $baseDir
    ));
}

// Clear static page cache
if (!is_dir('data/cache')) {
    mkdir('data/cache');
}

$command = '/usr/local/zend/bin/php -d date.timezone=America/Chicago public/index.php phlysimplepage cache clear all';
echo "\nExecuting `$command`\n";
system($command);

// Update github stats
$command = '/usr/local/zend/bin/php -d date.timezone=America/Chicago public/index.php githubfeed fetch';
echo "\nExecuting `$command`\n";
system($command);

// Update comics
$command = '/usr/local/zend/bin/php -d date.timezone=America/Chicago vendor/phly/phly-comic/bin/phly-comic.php fetch-all';
echo "\nExecuting `$command`\n";
system($command);

// Ensure data directory is writeable by the web server
$command = 'chmod -R a+rwX data';
echo "\nExecuting `$command`\n";
system($command);
