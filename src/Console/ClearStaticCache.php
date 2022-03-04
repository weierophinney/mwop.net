<?php

declare(strict_types=1);

namespace Mwop\Console;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function getcwd;
use function realpath;
use function sprintf;
use function unlink;

class ClearStaticCache extends Command
{
    private const PATH_TEMPLATE = '%s/data/cache';

    protected function configure(): void
    {
        $this->setDescription('Clear the static cache.');
        $this->setHelp('Clear any cached content.');
        $this->addOption(
            'path',
            'p',
            InputOption::VALUE_REQUIRED,
            'Application root path',
            realpath(getcwd())
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = sprintf(self::PATH_TEMPLATE, $input->getOption('path'));
        $io   = new SymfonyStyle($input, $output);

        $io->title(sprintf('Clearing static cache located in %s', $path));

        $rdi = new RecursiveDirectoryIterator($path);
        $rii = new RecursiveIteratorIterator(
            $rdi,
            RecursiveIteratorIterator::CHILD_FIRST | RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($rii as $file) {
            if (
                ! $file instanceof SplFileInfo
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
