<?php

declare(strict_types=1);

namespace Mwop\Blog\Console;

use Mwop\Blog\Mapper\MapperInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function file_put_contents;
use function sprintf;

class TagCloud extends Command
{
    public function __construct(private MapperInterface $mapper)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('blog:tag-cloud');
        $this->setDescription('Generate tag cloud.');
        $this->setHelp('Generate a template containing the tag cloud for the blog.');

        $this->addOption(
            'output',
            'o',
            InputOption::VALUE_REQUIRED,
            'Output file to which to write the tag cloud',
            'data/shared/tag-cloud.phtml'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Creating tag cloud');

        $output = $input->getOption('output');

        $cloud  = $this->mapper->fetchTagCloud();
        $markup = sprintf(
            "<h4 class=\"text-2xl font-semibold\">Tag Cloud</h4>\n%s",
            $cloud->render()
        );

        file_put_contents($output, $markup);

        $io->success('Created tag cloud.');

        return 0;
    }
}
