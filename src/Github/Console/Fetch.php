<?php

declare(strict_types=1);

namespace Mwop\Github\Console;

use Mwop\Github\AtomReader;
use Mwop\Github\ItemList;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

/**
 * Fetch github user activity links
 */
class Fetch extends Command
{
    public function __construct(
        private AtomReader $reader,
        private ItemList $itemList,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('github:fetch-activity');
        $this->setDescription('Fetch GitHub activity stream.');
        $this->setHelp('Fetch GitHub activity stream and generate links for the home page.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Retrieving GitHub activity');

        try {
            $data = $this->reader->read();
        } catch (Throwable $e) {
            $io->error('Failed to retrieve github activiity');
            $io->writeln($e->getMessage());
            $io->writeln($e->getTraceAsString());
            return 1;
        }

        $this->itemList->write($data['links']);

        $io->success('Retrieved GitHub activity.');

        return 0;
    }
}
