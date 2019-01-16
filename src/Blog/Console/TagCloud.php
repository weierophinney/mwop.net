<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog\Console;

use Mwop\Blog\Mapper\MapperInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class TagCloud extends Command
{
    private $mapper;

    public function __construct(MapperInterface $mapper)
    {
        $this->mapper = $mapper;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('blog:tag-cloud');
        $this->setDescription('Generate tag cloud.');
        $this->setHelp('Generate a template containing the tag cloud for the blog.');

        $this->addOption(
            'output',
            'o',
            InputOption::VALUE_REQUIRED,
            'Output file to which to write the tag cloud',
            'data/tag-cloud.phtml'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Creating tag cloud');

        $output = $input->getOption('output');

        $cloud  = $this->mapper->fetchTagCloud();
        $markup = sprintf(
            "<h4>Tag Cloud</h4>\n<div class=\"cloud\">%s</div>",
            $cloud->render()
        );

        file_put_contents($output, $markup);

        $io->success('Created tag cloud.');

        return 0;
    }
}
