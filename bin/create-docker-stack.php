#!/usr/bin/env php
<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

const REPOS = ['mwopphp', 'mwopnginx'];
const STACKFILE = 'docker-stack.yml';
const TAGSCRIPT = './bin/get-latest-tag.php';
const TEMPLATE = 'docker-stack.yml.dist';
const USER = 'mwop';

chdir(dirname(__DIR__));

$substitutions = [];
foreach (REPOS as $repo) {
    $command = sprintf('%s %s %s', TAGSCRIPT, USER, $repo);
    exec($command, $output, $return);
    if ($return !== 0) {
        fwrite(STDERR, implode($output, PHP_EOL));
        exit($return);
    }

    $substitutions[sprintf('{%s}', $repo)] = array_shift($output);
}

$stackFile = file_get_contents(TEMPLATE);
$stackFile = str_replace(array_keys($substitutions), array_values($substitutions), $stackFile);

file_put_contents(STACKFILE, $stackFile);
