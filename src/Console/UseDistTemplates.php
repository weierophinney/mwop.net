<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function copy;
use function getcwd;
use function realpath;
use function sprintf;

class UseDistTemplates extends Command
{
    private $distFileMap = [
        'src/Blog/templates/scripts.phtml.dist' => 'src/Blog/templates/scripts.phtml',
        'src/Blog/templates/styles.phtml.dist'  => 'src/Blog/templates/styles.phtml',
        'templates/layout/scripts.phtml.dist'   => 'templates/layout/scripts.phtml',
        'templates/layout/styles.phtml.dist'    => 'templates/layout/styles.phtml',
    ];

    protected function configure(): void
    {
        $this->setName('asset:use-dist-templates');
        $this->setDescription('Use dist templates.');
        $this->setHelp('Enable usage of distribution templates (optimizing CSS and JS).');

        $this->addOption(
            'path',
            'p',
            InputOption::VALUE_REQUIRED,
            'Base path of the application; templates are expected at $path/templates/',
            realpath(getcwd())
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io   = new SymfonyStyle($input, $output);
        $path = $input->getOption('path');

        $io->title('Enabling dist templates');

        foreach ($this->distFileMap as $source => $target) {
            $source = sprintf('%s/%s', $path, $source);
            $target = sprintf('%s/%s', $path, $target);
            copy($source, $target);
        }

        $io->success('Enabled dist templates');

        return 0;
    }
}
