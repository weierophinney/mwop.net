<?php

declare(strict_types=1);

namespace Mwop\Feed\Webhook;

use Mezzio\ProblemDetails\Exception\CommonProblemDetailsExceptionTrait;
use Mezzio\ProblemDetails\Exception\ProblemDetailsExceptionInterface;
use Mwop\JsonValidate;
use Mwop\QueueableEvent;
use RuntimeException;

class Payload implements QueueableEvent
{
    private function __construct(
        public readonly array $payload,
    ) {
    }

    public static function fromJSON(string $json): self
    {
        if (! (new JsonValidate())($json)) {
            throw new class extends RuntimeException implements ProblemDetailsExceptionInterface {
                use CommonProblemDetailsExceptionTrait;

                public function __construct()
                {
                    $status      = 400;
                    $description = 'Invalid JSON payload';

                    parent::__construct($description, $status);

                    $this->status = $status;
                    $this->detail = $description;
                    $this->title  = 'Bad Request';
                    $this->type   = 'https://httpstatuses.io/400';
                }
            };
        }

        return new self(json_decode($json, true));
    }

    public static function fromDataArray(array $data): self
    {
        return new self($data);
    }

    public function jsonSerialize(): array
    {
        return $this->payload;
    }
}
