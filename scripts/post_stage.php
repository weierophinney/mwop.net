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
$output  = shell_exec($command);

// Ensure data directory is writeable by the web server
$command = 'chmod -R a+rwX data';
$output  = shell_exec($command);
