<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CopyAssetSymlinks extends Command
{
    protected function configure()
    {
        $this->setName('asset:copy-symlinks');
        $this->setDescription('Copy assets.');
        $this->setHelp('Copy assets installed by npm into the public tree.');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Copying asset symlinks');

        foreach (CreateAssetSymlinks::ASSET_MAP as $origin => $target) {
            if (file_exists($target)) {
                unlink($target);
            }

            $origin = str_replace('../../', '', $origin);

            copy($origin, $target);
        }

        $io->success('Copyied asset symlinks');

        return 0;
    }
}
