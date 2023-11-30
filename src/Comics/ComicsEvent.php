<?php

declare(strict_types=1);

namespace Mwop\Comics;

use Mwop\App\EventDispatcher\QueueableEvent;

class ComicsEvent implements QueueableEvent
{
    public static function fromJSON(string $json): self
    {
        return new self();
    }

    public static function fromDataArray(array $data): self
    {
        return new self();
    }

    public function jsonSerialize(): array
    {
        return [];
    }
}
