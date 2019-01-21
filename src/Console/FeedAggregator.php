<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Console;

use Exception;
use Mwop\Util\Collection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Zend\Feed\Reader\Reader as FeedReader;
use Zend\Feed\Reader\Feed\FeedInterface;

class FeedAggregator extends Command
{
    const CACHE_FILE = '%s/config/autoload/homepage.local.php';

    private $configFormat = <<< EOC
<?php
return [
    'homepage' => [
        'posts' => [
%s        ],
    ],
];

EOC;

    private $feeds;

    private $itemFormat = <<< EOF
            [
                'title'    => '%s',
                'link'     => '%s',
                'favicon'  => '%s',
                'sitename' => '%s',
                'siteurl'  => '%s',
            ],

EOF;

    private $toRetrieve;

    private $status;

    public function __construct(array $feeds, int $toRetrieve)
    {
        $this->feeds = Collection::create($feeds);
        $this->toRetrieve = $toRetrieve;
        parent::__construct();
    }

    protected function configure()
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

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $this->status = 0;
        $io = new SymfonyStyle($input, $output);
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

    private function getEntries(SymfonyStyle $io) : Collection
    {
        return $this->feeds
            ->reduce(function ($entries, $feedInfo) use ($io) {
                return $entries->append($this->marshalEntries($feedInfo, $io));
            }, Collection::create([]))
            ->sort(function ($a, $b) {
                return ($b['date-created'] <=> $a['date-created']);
            })
            ->slice($this->toRetrieve);
    }

    private function generateFilename(string $path) : string
    {
        return sprintf(self::CACHE_FILE, $path);
    }

    private function generateContent(Collection $entries) : string
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

    private function marshalEntries(array $feedInfo, SymfonyStyle $io) : Collection
    {
        $feedUrl  = $feedInfo['url'];
        $logo     = $feedInfo['favicon'] ?? 'https://mwop.net/images/favicon/favicon-16x16.png';
        $siteName = $feedInfo['sitename'] ?? '';
        $siteUrl  = $feedInfo['siteurl'] ?? '#';
        $filters  = $feedInfo['filters'] ?? [];
        $each     = $feedInfo['each'] ?? function ($item) {
        };

        $io->text(sprintf('<info>Retrieving %s</>', $feedUrl));
        $io->progressStart();

        try {
            $feed = preg_match('#^https?://#', $feedUrl)
                ? $this->getFeedFromRemoteUrl($feedUrl)
                : $this->getFeedFromLocalFile($feedUrl);
        } catch (\Throwable $e) {
            $io->progressFinish();
            $this->reportException($e, $io);
            $io->error('Failed fetching one or more feeds');
            $this->status = 1;
            return new Collection([]);
        }

        $entries = Collection::create($feed)
            ->filterChain($filters)
            ->slice(5)
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

    private function getFeedFromLocalFile(string $file) : FeedInterface
    {
        return FeedReader::importString(file_get_contents($file));
    }

    private function getFeedFromRemoteUrl(string $url) : FeedInterface
    {
        return FeedReader::import($url);
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
