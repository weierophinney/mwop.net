<?php

declare(strict_types=1);

namespace Mwop\Feed\Webhook;

use JsonException;
use Mwop\Feed\FeedItem;
use Mwop\Feed\HomepagePostsList;
use Psr\Log\LoggerInterface;

use function json_decode;
use function sprintf;

use const JSON_THROW_ON_ERROR;

class PayloadListener
{
    public function __construct(
        private HomepagePostsList $postsList,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(Payload $payload): void
    {
        $item = FeedItem::fromArray($this->parsePayloadJson($payload));
        if (null === $item) {
            return;
        }

        $this->logger->info(sprintf('Adding RSS entry "%s" (%s)', $item->title, $item->link));
        $posts = $this->postsList->read();
        $posts->prepend($item);
        $this->postsList->write($posts);
    }

    private function parsePayloadJson(Payload $payload): array
    {
        try {
            return json_decode($payload->json, true, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->logger->warning(sprintf(
                "Unable to parse Feed RSS item webhook payload: %s\nPayload: %s",
                $e->getMessage(),
                $payload->json
            ));
            return [];
        }
    }
}
