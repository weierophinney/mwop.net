<?php

declare(strict_types=1);

namespace Mwop\Console;

use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\Mapper\TreeMapper;
use Laminas\Feed\Reader\Entry\EntryInterface;
use Laminas\Feed\Reader\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FetchMastodonFeed extends Command
{
    private const FEED_URI = 'https://phpc.social/users/mwop.rss';
    public function __construct(
        private TreeMapper $mapper,
        private int $maxItems = 10,
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

        $io = new SymfonyStyle($input, $output);
        $io->title('Retrieving Mastodon activity');

        $feed = Reader::import(self::FEED_URI);
        $entries = Mastodon\Collection::make($feed)
            ->slice(0, $this->maxItems)
            ->map(fn (EntryInterface $entry): Mastodon\Entry => $this->mapper->map(Mastodon\Entry::class, Source::array([
                'link'    => $entry->getLink(),
                'content' => $entry->getDescription(),
                'created' => $entry->getDateCreated(),
            ])));

        $io->info(sprintf('Writing entries to %s', $path));
        file_put_contents($path, $entries->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        
        $io->success('Done!');

        return 0;
    }
}
