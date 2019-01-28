<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function copy;
use function file_exists;
use function str_replace;
use function unlink;

class CopyAssetSymlinks extends Command
{
    protected function configure() : void
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

        $io->success('Copied asset symlinks');

        return 0;
    }
}
