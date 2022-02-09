<?php

declare(strict_types=1);

namespace Mwop\Blog\Flickr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function sprintf;

class SearchCommand extends Command
{
    private const HELP = <<<'END'
        Allows performing a fulltext search against the Flickr API to get a list of potential photos.
        You may specify how many photos to retrieve at a time, as well, as which page of photos
        (the latter is useful if you do not find suitable results in the first or later pages).

        The list returned will include the image title, its ID and secret (for use with blog:photo-fetch),
        and a URL for preview.

        END;

    public function __construct(
        private Photos $photos,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Search for photos on Flickr');
        $this->setHelp(self::HELP);
        $this->addArgument('search', InputArgument::REQUIRED, 'Search term(s) to use');
        $this->addOption('count', 'c', InputOption::VALUE_REQUIRED, 'How many results to return', 10);
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

        $results = $this->photos->search($search, $page, $perPage);
        foreach ($results as $photo) {
            $output->writeln($photo);
        }

        return 0;
    }
}
