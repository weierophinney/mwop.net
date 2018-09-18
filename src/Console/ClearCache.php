<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Console;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ClearCache extends Command
{
    const PATH_TEMPLATE = '%s/data/cache';

    protected function configure()
    {
        $this->setName('clear-cache');
        $this->setDescription('Clear the static cache.');
        $this->setHelp('Clear any cached content.');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Clearing static cache');

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

        $io->success('Static cache cleared');

        return 0;
    }
}
