<?php

declare(strict_types=1);

namespace Mwop\Feed;

use JsonException;
use Psr\Log\LoggerInterface;

use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function getcwd;
use function json_decode;
use function json_encode;
use function realpath;
use function sprintf;

use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

class HomepagePostsList
{
    private string $listLocation;

    public function __construct(
        private readonly int $limit,
        private LoggerInterface $logger,
    ) {
        $this->listLocation = sprintf(Console\FeedAggregator::CACHE_FILE, realpath(getcwd()));
    }

    public function read(): FeedCollection
    {
        if (! file_exists($this->listLocation)) {
            return new FeedCollection([]);
        }

        $json = file_get_contents($this->listLocation);

        try {
            $items = json_decode($json, true, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->logger->warning(sprintf(
                "Unable to parse home page activity feed: %s\nPayload: %s",
                $e->getMessage(),
                $json,
            ));
            $items = [];
        }

        return FeedCollection::make($items)
            ->map(fn (array $item): FeedItem => FeedItem::fromArray($item));
    }

    public function write(FeedCollection $entries): void
    {
        try {
            $json = json_encode(
                $entries->slice(0, $this->limit)->toArray(),
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR
            );
        } catch (JsonException $e) {
            $this->logger->warning(sprintf(
                'Unable to serialize GitHub activity feed: %s',
                $e->getMessage(),
            ));
            return;
        }

        file_put_contents($this->listLocation, $json);
    }
}
