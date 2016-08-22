<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Console;

use Exception;
use Mwop\Collection;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Console\ColorInterface as Color;
use Zend\Feed\Reader\Reader as FeedReader;
use Zend\Feed\Reader\Feed\FeedInterface;
use ZF\Console\Route;

class FeedAggregator
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

    public function __construct(array $feeds, int $toRetrieve)
    {
        $this->feeds = Collection::create($feeds);
        $this->toRetrieve = $toRetrieve;
    }

    public function __invoke(Route $route, Console $console) : int
    {
        $console->writeLine('Aggregating feed data...', Color::GREEN);
        file_put_contents(
            $this->generateFilename($route->getMatchedParam('path')),
            $this->generateContent($this->getEntries($console))
        );
        $console->writeLine('[DONE]', Color::GREEN);
        return 0;
    }

    private function getEntries(Console $console) : Collection
    {
        return $this->feeds
            ->reduce(function ($entries, $feedInfo) use ($console) {
                return $entries->append($this->marshalEntries($feedInfo, $console));
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

    private function marshalEntries(array $feedInfo, Console $console) : Collection
    {
        $feedUrl  = $feedInfo['url'];
        $logo     = $feedInfo['favicon'] ?? 'https://mwop.net/images/favicon/favicon-16x16.png';
        $siteName = $feedInfo['sitename'] ?? '';
        $siteUrl  = $feedInfo['siteurl'] ?? '#';
        $filters  = $feedInfo['filters'] ?? [];
        $each     = $feedInfo['each'] ?? function ($item) {
        };

        $message = sprintf('    Retrieving %s... ', $feedUrl);
        $length  = strlen($message);
        $console->write($message, Color::BLUE);

        try {
            $feed = preg_match('#^https?://#', $feedUrl)
                ? $this->getFeedFromRemoteUrl($feedUrl)
                : $this->getFeedFromLocalFile($feedUrl);
        } catch (\Throwable $e) {
            $this->reportException($e, $console);
            $this->writeLine('[ FAIL ]', Color::RED);
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

        $this->reportComplete($console, $length);
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

    private function reportException(Exception $e, Console $console)
    {
        $console->writeLine('An exception occurred:', Color::RED);

        do {
            $console->writeLine(sprintf(
                '%s: %s (in %s:%d)',
                (int) $e->getCode(),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            ));
            $console->writeLine('Trace:');
            $console->writeLine($e->getTraceAsString());
            $e = $e->getPrevious();
        } while ($e);
    }

    private function reportComplete(Console $console, int $length)
    {
        $width = $console->getWidth();
        if (($length + 8) > $width) {
            $console->writeLine('');
            $length = 0;
        }
        $spaces  = $width - $length - 8;
        $spaces  = ($spaces > 0) ? $spaces : 0;
        $console->writeLine(str_repeat('.', $spaces) . '[ DONE ]', Color::GREEN);
    }
}
