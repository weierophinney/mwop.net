<?php

declare(strict_types=1);

namespace Mwop\Mastodon\Console;

use Mwop\Mastodon\FetchMastodonFeed as FetchMastodonFeedService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function sprintf;

class FetchMastodonFeed extends Command
{
    public function __construct(
        private FetchMastodonFeedService $feed,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Fetch Mastodon RSS feed');
        $this->setHelp('Fetch Mastodon social activity and generate links for the home page.');

        $this->addOption(
            name: 'path',
            shortcut: 'p',
            mode: InputOption::VALUE_REQUIRED,
            description: 'Path to which to write the feed data',
            default: 'data/mastodon.json'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getOption('path');
        $io   = new SymfonyStyle($input, $output);

        $io->title('Retrieving Mastodon activity');
        $entries = $this->feed->fetchEntries();
        $io->info(sprintf('Writing entries to %s', $path));
        $this->feed->cacheEntries($entries, $path);
        $io->success('Done!');

        return 0;
    }
}
