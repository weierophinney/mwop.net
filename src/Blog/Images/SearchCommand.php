<?php

declare(strict_types=1);

namespace Mwop\Blog\Images;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function sprintf;

class SearchCommand extends Command
{
    private const HELP = <<<'END'
        Allows performing a fulltext image search to get a list of potential
        images. You may specify how many to retrieve at a time, as well, as which
        page (the latter is useful if you do not find suitable results in the first
        or later pages).

        The list returned will include the image url, the creator, and an
        attribution URL.

        The best way to use this is:

        $ laminas blog:image-search {some text} | vim -c 'set ft=markdown' -
        
        In vim, then:

        :MarkdownPreview

        END;

    public function __construct(
        private Images $images,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Search for images');
        $this->setHelp(self::HELP);
        $this->addArgument('search', InputArgument::REQUIRED, 'Search term(s) to use');
        $this->addOption('count', 'c', InputOption::VALUE_REQUIRED, 'How many results to return', 25);
        $this->addOption('page', 'p', InputOption::VALUE_REQUIRED, 'Which page of results to return', 1);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $search  = $input->getArgument('search');
        $perPage = (int) $input->getOption('count');
        $page    = (int) $input->getOption('page');

        $output->writeln(sprintf(
            '<info>Searching for "%s"...</info>',
            $search
        ));

        $results = $this->images->search($search, $page, $perPage);

        $output->writeln(sprintf('<info>Showing %d results:</info>', $results->count()));

        foreach ($results as $image) {
            $output->writeln($image->__toString());
        }

        return 0;
    }
}
