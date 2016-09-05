<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Console;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Zend\Console\Adapter\AdapterInterface as Console;
use ZF\Console\Route;

class ClearCache
{
    const PATH_TEMPLATE = '%s/data/cache';

    public function __invoke(Route $route, Console $console) : int
    {
        $console->write('Clearing static cache... ');

        $rdi = new RecursiveDirectoryIterator(sprintf(self::PATH_TEMPLATE, $route->getMatchedParam('path')));
        $rii = new RecursiveIteratorIterator(
            $rdi,
            RecursiveIteratorIterator::CHILD_FIRST | RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($rii as $file) {
            if (! $file instanceof SplFileInfo
                || $file->isDir()
            ) {
                continue;
            }

            if ($file->getFilename() === '.placeholder') {
                continue;
            }

            unlink($file->getRealPath());
        }

        $console->writeLine('[DONE]');

        return 0;
    }
}
