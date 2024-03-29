<?php

declare(strict_types=1);

namespace Mwop\Art\Console;

use League\Flysystem\FilesystemOperator;
use League\Flysystem\MountManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class FetchPhotoDatabase extends Command
{
    private MountManager $filesystem;

    public function __construct(
        FilesystemOperator $app,
        FilesystemOperator $remote,
        private string $database,
    ) {
        $this->filesystem = new MountManager([
            'app'    => $app,
            'remote' => $remote,
        ]);
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Fetch the remote photo database for use locally');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->filesystem->copy('remote://' . $this->database, 'app://' . $this->database);

            // If WAL pragma is enabled, copy that file
            if ($this->filesystem->has('remote://' . $this->database . '-wal')) {
                $this->filesystem->copy(
                    'remote://' . $this->database . '-wal',
                    'app://' . $this->database . '-wal'
                );
            }
        } catch (Throwable $e) {
            $output->writeln('<error>Failed to fetch remote photo database for local use</error>');
            do {
                $output->writeln($e->getMessage());
                $output->writeln($e->getTraceAsString());
                $e = $e->getPrevious();
            } while ($e);

            return 1;
        }

        return 0;
    }
}
