<?php

declare(strict_types=1);

namespace Mwop\Art\Webhook;

use JsonException;
use Mwop\Art\Photo;
use Psr\Log\LoggerInterface;

use function json_decode;
use function json_encode;
use function sprintf;

use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

class PayloadListener
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(Payload $payload): void
    {
        $photo = Photo::fromArray($this->parsePayloadJson($payload));
        if (null === $photo) {
            $this->logger->warning(sprintf(
                'Empty Instagram payload detected: %s',
                $payload->json
            ));
            return;
        }

        $this->logger->info(sprintf(
            "Received Instagram payload:\n%s",
            json_encode(
                $photo,
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
            )
        ));
    }

    private function parsePayloadJson(Payload $payload): array
    {
        try {
            return json_decode($payload->json, true, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->logger->warning(sprintf(
                "Unable to parse Instagram webhook payload: %s\nPayload: %s",
                $e->getMessage(),
                $payload->json
            ));
            return [];
        }
    }
}
