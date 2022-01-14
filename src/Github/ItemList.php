<?php

declare(strict_types=1);

namespace Mwop\Github;

use Illuminate\Support\Collection;
use JsonException;
use Psr\Log\LoggerInterface;

use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function json_decode;
use function json_encode;
use function sprintf;

use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

class ItemList
{
    public function __construct(
        private readonly string $listLocation,
        private readonly int $limit,
        private LoggerInterface $logger,
    ) {
    }

    public function read(): Collection
    {
        if (! file_exists($this->listLocation)) {
            return new Collection([]);
        }

        $json = file_get_contents($this->listLocation);

        try {
            $items = json_decode($json, true, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->logger->warning(sprintf(
                "Unable to parse GitHub activity feed: %s\nPayload: %s",
                $e->getMessage(),
                $json,
            ));
            $items = [];
        }

        return new Collection($items);
    }

    public function write(Collection $entries): void
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
