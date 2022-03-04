<?php

declare(strict_types=1);

namespace Mwop\Console;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ClearResponseCache extends Command
{
    public function __construct(private CacheItemPoolInterface $cache)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Clear the response cache');
        $this->setHelp('Clear the response cache');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Removing cached responses');

        if (! $this->cache->clear()) {
            $io->error('Response cache pool indicated unsuccessful clear operation');
            return 1;
        }

        $io->success('Response cache cleared');
        return 0;
    }
}
