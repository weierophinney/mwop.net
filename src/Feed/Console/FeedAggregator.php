<?php

declare(strict_types=1);

namespace Mwop\Feed\Console;

use DateTimeInterface;
use Error;
use Http\Discovery\HttpClientDiscovery;
use Laminas\Feed\Reader\Entry\EntryInterface;
use Laminas\Feed\Reader\Feed\FeedInterface;
use Laminas\Feed\Reader\Reader as FeedReader;
use Mwop\Feed\FeedCollection;
use Mwop\Feed\FeedItem;
use Psr\Http\Message\RequestFactoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

use function file_get_contents;
use function file_put_contents;
use function get_debug_type;
use function getcwd;
use function is_callable;
use function preg_match;
use function printf;
use function realpath;
use function sprintf;

use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

class FeedAggregator extends Command
{
    /** @var string */
    public const CACHE_FILE = '%s/data/homepage.posts.json';

    /** @var FeedCollection */
    private $feeds;

    /** @var int */
    private $status;

    public function __construct(
        array $feeds,
        private int $toRetrieve,
        private RequestFactoryInterface $requestFactory
    ) {
        $this->feeds = FeedCollection::make($feeds);
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('homepage-feeds');
        $this->setDescription('Fetch homepage feed data.');
        $this->setHelp('Fetch feed data for homepage activity stream.');

        $this->addOption(
            'path',
            'p',
            InputOption::VALUE_REQUIRED,
            'Application root path',
            realpath(getcwd())
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->status = 0;
        $io           = new SymfonyStyle($input, $output);
        $io->title('Aggregating feed data for home page');
        file_put_contents(
            $this->generateFilename($input->getOption('path')),
            $this->generateContent($this->getEntries($io))
        );

        if ($this->status === 0) {
            $io->success('Aggregated home page feed data.');
        }

        return $this->status;
    }

    private function getEntries(SymfonyStyle $io): FeedCollection
    {
        return $this->feeds
            ->reduce(
                // phpcs:ignore Generic.Files.LineLength.TooLong
                fn (FeedCollection $entries, array $feedInfo): FeedCollection => $entries->merge($this->marshalEntries($feedInfo, $io)),
                FeedCollection::make([])
            )
            ->sortByDesc(fn (FeedItem $item): DateTimeInterface => $item->created)
            ->slice(0, $this->toRetrieve);
    }

    private function generateFilename(string $path): string
    {
        return sprintf(self::CACHE_FILE, $path);
    }

    private function generateContent(FeedCollection $entries): string
    {
        return $entries
            ->values()
            ->toJson(
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
            );
    }

    private function marshalEntries(array $feedInfo, SymfonyStyle $io): FeedCollection
    {
        $feedUrl     = $feedInfo['url'];
        $logo        = $feedInfo['favicon'] ?? 'https://mwop.net/images/favicon/favicon-16x16.png';
        $siteName    = $feedInfo['sitename'] ?? '';
        $siteUrl     = $feedInfo['siteurl'] ?? '#';
        $filters     = $feedInfo['filters'] ?? [];
        $normalizers = $feedInfo['normalizers'] ?? [];
        $each        = $feedInfo['each'] ?? function (mixed $item): void {
            printf("- %s\n", get_debug_type($item));
        };

        $io->text(sprintf('<info>Retrieving %s</>', $feedUrl));
        $io->progressStart();

        try {
            $feed = preg_match('#^https?://#', $feedUrl)
                ? $this->getFeedFromRemoteUrl($feedUrl, $normalizers)
                : $this->getFeedFromLocalFile($feedUrl);
        } catch (Throwable $e) {
            $io->progressFinish();
            $this->reportException($e, $io);
            $io->error('Failed fetching one or more feeds');
            $this->status = 1;
            return new FeedCollection([]);
        }

        $entries = FeedCollection::make($feed)
            ->filterChain($filters)
            ->each($each)
            ->map(fn (EntryInterface $entry): FeedItem => new FeedItem(
                title: $entry->getTitle(),
                link: $entry->getLink(),
                favicon: $logo,
                sitename: $siteName,
                siteurl: $siteUrl,
                created: $entry->getDateCreated(),
            ));

        $io->progressFinish();

        return $entries;
    }

    private function getFeedFromLocalFile(string $file): FeedInterface
    {
        return FeedReader::importString(file_get_contents($file));
    }

    private function getFeedFromRemoteUrl(string $url, array $normalizers): FeedInterface
    {
        $client      = HttpClientDiscovery::find();
        $response    = $client->sendRequest($this->requestFactory->createRequest('GET', $url));
        $feedContent = $response->getBody()->getContents();

        foreach ($normalizers as $normalizer) {
            if (! is_callable($normalizer)) {
                continue;
            }
            $feedContent = $normalizer($feedContent);
        }

        return FeedReader::importString($feedContent);
    }

    private function reportException(Throwable $e, SymfonyStyle $io)
    {
        $io->caution(sprintf('An %s occurred:', $e instanceof Error ? 'error' : 'exception'));

        do {
            $io->caution(sprintf(
                '%s: %s (in %s:%d)',
                (int) $e->getCode(),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            ));
            $io->caution('Trace:');
            $io->caution($e->getTraceAsString());
            $e = $e->getPrevious();
        } while ($e);
    }
}
