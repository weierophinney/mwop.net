<?php

declare(strict_types=1);

namespace Mwop\Feed\Webhook;

use CuyZ\Valinor\Mapper\MappingError;
use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\Mapper\TreeMapper;
use Mwop\App\HomePageCacheExpiration;
use Mwop\Feed\FeedItem;
use Mwop\Feed\HomepagePostsList;
use Mwop\Feed\InvalidFeedItem;
use Psr\Log\LoggerInterface;

use function sprintf;

class PayloadListener
{
    public function __construct(
        private HomepagePostsList $postsList,
        private LoggerInterface $logger,
        private HomePageCacheExpiration $expireHomePageCache,
        private TreeMapper $mapper,
    ) {
    }

    public function __invoke(Payload $payload): void
    {
        $item = $this->parsePayloadJson($payload);
        if ($item instanceof InvalidFeedItem) {
            return;
        }

        $this->logger->info(sprintf('Adding RSS entry "%s" (%s)', $item->title, $item->link));
        $posts = $this->postsList->read();
        $posts->prepend($item);
        $this->postsList->write($posts);

        ($this->expireHomePageCache)();
    }

    private function parsePayloadJson(Payload $payload): FeedItem
    {
        try {
            return $this->mapper->map(FeedItem::class, Source::array($payload->payload));
        } catch (MappingError $e) {
            $this->logger->warning(sprintf(
                "Unable to parse Feed RSS item webhook payload: %s\nPayload: %s",
                $e->getMessage(),
                $payload->payload
            ));

            while (null !== ($e = $e->getPrevious())) {
                $this->logger->warning(sprintf('Previous message: %s', $e->getMessage()));
            }

            return new InvalidFeedItem();
        }
    }
}
