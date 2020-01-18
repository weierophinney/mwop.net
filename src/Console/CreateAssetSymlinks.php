<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function file_exists;
use function symlink;

class CreateAssetSymlinks extends Command
{
    private const ASSET_MAP = [
        '../../node_modules/bootstrap/dist/js/bootstrap.js'              => 'public/js/bootstrap.js',
        '../../node_modules/jquery/dist/jquery.js'                       => 'public/js/jquery.js',
        '../../node_modules/autocomplete.js/dist/autocomplete.jquery.js' => 'public/js/autocomplete.jquery.js',
    ];

    protected function configure(): void
    {
        $this->setName('asset:create-symlinks');
        $this->setDescription('Symlink assets.');
        $this->setHelp('Symlink assets installed by npm into the public tree.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Creating asset symlinks');

        foreach (self::ASSET_MAP as $target => $link) {
            if (! file_exists($link)) {
                symlink($target, $link);
            }
        }

        $io->success('Created asset symlinks');

        return 0;
    }
}
