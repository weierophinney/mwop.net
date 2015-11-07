<?php
namespace Mwop;

function getPhpExecutable()
{
    $php = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
        ? escapeshellarg('c:/Program Files (x86)/ZendServer/bin/php.exe')
        : '/usr/local/zend/bin/php';
    return $php . ' -d date.timezone=America/Chicago';
}
