<?php
namespace Mwop\Console;

use Mwop\Collection;
use Zend\Console\ColorInterface as Color;
use Zend\Feed\Reader\Reader as FeedReader;

class FeedAggregator
{
    const CACHE_FILE = '%s/data/cache/homepage-feed-items.php';

    private $feeds;

    public function __construct(array $feeds)
    {
        $this->feeds = Collection::create($feeds);
    }

    public function __invoke($route, $console)
    {
        $console->writeLine('Aggregating feed data...', Color::GREEN);
        file_put_contents(
            $this->generateFilename($route->getMatchedParam('path')),
            $this->generateContent($this->getEntries($console))
        );
        $console->writeLine('[DONE]', Color::GREEN);
    }

    private function getEntries($console)
    {
        return $this->feeds
            ->reduce(function ($entries, $feedUrl) use ($console) {
                return $entries->append($this->marshalEntries($feedUrl, $console));
            }, Collection::create([]))
            ->sort(function ($a, $b) {
                return ($b->getDateCreated() <=> $a->getDateCreated());
            })
            ->slice(5);
    }

    private function generateFilename($path)
    {
        return sprintf(self::CACHE_FILE, $path);
    }

    private function generateContent(Collection $entries)
    {
        return sprintf(
            '<' . "?php\nreturn [\n%s];",
            $entries->reduce(function ($string, $entry) {
                return $string . sprintf(
                    "    [\n        'title' => '%s',\n        'link' => '%s',\n    ],\n",
                    $entry->getTitle(),
                    $entry->getLink()
                );
            }, '')
        );
    }

    private function marshalEntries($feedUrl, $console)
    {
        $filters = (is_array($feedUrl) && isset($feedUrl['filters']))
            ? $feedUrl['filters']
            : [];

        $each = (is_array($feedUrl) && isset($feedUrl['each']))
            ? $feedUrl['each']
            : function ($item) {
            };

        $feedUrl = is_array($feedUrl)
            ? $feedUrl['url']
            : $feedUrl;

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
            ->each($each);

        $this->reportComplete($console, $length);
        return $entries;
    }

    private function getFeedFromLocalFile($file)
    {
        return FeedReader::importString(file_get_contents($file));
    }

    private function getFeedFromRemoteUrl($url)
    {
        return FeedReader::import($url);
    }

    private function reportException($e, $console)
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

    private function reportComplete($console, $length)
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
