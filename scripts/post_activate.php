<?php
$baseDir = getenv('ZS_APPLICATION_BASE_DIR');

// Place a marker file indicating the application directory so cronjobs know 
// where to operate
if (!is_dir('/usr/local/zend/tmp/mwop.net')) {
    mkdir('/usr/local/zend/tmp/mwop.net', 0777, true);
}
file_put_contents('/usr/local/zend/tmp/mwop.net/path', $baseDir);
