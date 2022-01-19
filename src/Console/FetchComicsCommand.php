<?php

declare(strict_types=1);

namespace Mwop\Console;

use PhlyComic\Console\FetchAllComics;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchComicsCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'comics:for-site';

    public function __construct(
        private readonly array $exclusions,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Fetch comics for the website.');
        $this->setHelp('Delegates to the comics:fetch-all command, with a list of exclusions.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var FetchAllComics $fetchAllCommand */
        $fetchAllCommand = $this->getApplication()->find('comics:fetch-all');

        return $fetchAllCommand->run(
            new ArrayInput([
                '--exclude'   => $this->exclusions,
                '--output'    => 'data/comics.phtml',
                '--processes' => 5,
            ]),
            $output
        );
    }
}
