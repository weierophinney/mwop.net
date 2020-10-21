<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Console;

use Exception;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Laminas\Feed\Reader\Feed\FeedInterface;
use Laminas\Feed\Reader\Reader as FeedReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

use function file_get_contents;
use function file_put_contents;
use function getcwd;
use function preg_match;
use function realpath;
use function sprintf;

class FeedAggregator extends Command
{
    /** @var string */
    public const CACHE_FILE = '%s/data/homepage.posts.php';

    /** @var string */
    private $configFormat = <<<EOC
<?php
return [
%s
];

EOC;

    /** @var FeedCollection */
    private $feeds;

    /** @var string */
    private $itemFormat = <<<EOF
    [
        'title'    => '%s',
        'link'     => '%s',
        'favicon'  => '%s',
        'sitename' => '%s',
        'siteurl'  => '%s',
    ],

EOF;

    /** @var int */
    private $toRetrieve;

    /** @var int */
    private $status;

    public function __construct(array $feeds, int $toRetrieve)
    {
        $this->feeds      = FeedCollection::make($feeds);
        $this->toRetrieve = $toRetrieve;
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
            ->reduce(function ($entries, $feedInfo) use ($io) {
                return $entries->merge($this->marshalEntries($feedInfo, $io));
            }, FeedCollection::make([]))
            ->sortByDesc('date-created')
        /*
            ->each(function ($entry) {
                printf("- [%s] %s (%s)\n", $entry['date-created']->format('Y-m-d'), $entry['title'], $entry['link']);
            })
         */
            ->slice(0, $this->toRetrieve);
    }

    private function generateFilename(string $path): string
    {
        return sprintf(self::CACHE_FILE, $path);
    }

    private function generateContent(FeedCollection $entries): string
    {
        return sprintf(
            $this->configFormat,
            $entries->reduce(function ($string, $entry) {
                return $string . sprintf(
                    $this->itemFormat,
                    $entry['title'],
                    $entry['link'],
                    $entry['favicon'],
                    $entry['sitename'],
                    $entry['siteurl']
                );
            }, '')
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
        $each        = $feedInfo['each'] ?? function ($item) {
            printf("- %s\n", is_object($item) ? get_class($item) : gettype($item));
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
            ->map(function ($entry) use ($logo, $siteName, $siteUrl) {
                return [
                    'title'        => $entry->getTitle(),
                    'link'         => $entry->getLink(),
                    'date-created' => $entry->getDateCreated(),
                    'favicon'      => $logo,
                    'sitename'     => $siteName,
                    'siteurl'      => $siteUrl,
                ];
            });

        $io->progressFinish();

        return $entries;
    }

    private function getFeedFromLocalFile(string $file): FeedInterface
    {
        return FeedReader::importString(file_get_contents($file));
    }

    private function getFeedFromRemoteUrl(string $url, array $normalizers): FeedInterface
    {
        $client         = HttpClientDiscovery::find();
        $messageFactory = MessageFactoryDiscovery::find();
        $response       = $client->sendRequest($messageFactory->createRequest('GET', $url));
        $feedContent    = $response->getBody()->getContents();

        foreach ($normalizers as $normalizer) {
            if (! is_callable($normalizer)) {
                continue;
            }
            $feedContent = $normalizer($feedContent);
        }

        return FeedReader::importString($feedContent);
    }

    private function reportException(Exception $e, SymfonyStyle $io)
    {
        $io->caution('An exception occurred:');

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
