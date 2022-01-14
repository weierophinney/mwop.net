<?php

declare(strict_types=1);

namespace Mwop\Github\Console;

use Mwop\Github\AtomReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

use function file_put_contents;
use function json_encode;

/**
 * Fetch github user activity links
 */
class Fetch extends Command
{
    public function __construct(
        private ?AtomReader $reader = null,
        private string $defaultListFile = '',
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('github:fetch-activity');
        $this->setDescription('Fetch GitHub activity stream.');
        $this->setHelp('Fetch GitHub activity stream and generate links for the home page.');

        $this->addOption(
            'output',
            'o',
            InputOption::VALUE_REQUIRED,
            'Output file to which to write links',
            $this->defaultListFile,
        );
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

        file_put_contents(
            $input->getOption('output'),
            $this->createContentFromData($data),
        );

        $io->success('Retrieved GitHub activity.');

        return 0;
    }

    /**
     * Create content to write to the output file
     *
     * Uses the passed data to generate content.
     */
    private function createContentFromData(array $data): string
    {
        return json_encode($data['links'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
