<?php
$baseDir = getenv('ZS_APPLICATION_BASE_DIR');
if (! chdir($baseDir)) {
    throw new Exception(sprintf(
        'Unable to change directory to %s',
        $baseDir
    ));
}

$php = '/usr/local/zend/bin/php -d date.timezone=America/Chicago';

// Setup blog database
$command = sprintf('%s bin/mwop.net.php seed-blog-db', $php);
echo "\nExecuting `$command`\n";
system($command);

// Generate tag cloud
$command = sprintf('%s bin/mwop.net.php tag-cloud', $php);
echo "\nExecuting `$command`\n";
system($command);

// Generate feeds
$command = sprintf('%s bin/mwop.net.php feed-generator', $php);
echo "\nExecuting `$command`\n";
system($command);

// Cache blog posts
$command = sprintf('%s bin/mwop.net.php cache-posts', $php);
echo "\nExecuting `$command`\n";
system($command);

// Seed github links
$command = sprintf('%s bin/mwop.net.php github-links', $php);
echo "\nExecuting `$command`\n";
system($command);

// Seed comics
$command = sprintf('%s vendor/phly/phly-comic/bin/phly-comic.php fetch-all', $php);
echo "\nExecuting `$command`\n";
system($command);

// Ensure data directory is writeable by the web server
$command = 'chmod -R a+rwX ./data';
echo "\nExecuting `$command`\n";
system($command);
