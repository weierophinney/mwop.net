<?php
namespace Mwop\Console;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class ClearCache
{
    const PATH_TEMPLATE = '%s/data/cache';

    public function __invoke($route, $console)
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

            unlink($file->getRealPath());
        }

        $console->writeLine('[DONE]');
    }
}
