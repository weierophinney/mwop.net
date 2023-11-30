<?php

declare(strict_types=1);

namespace Mwop\App\EventDispatcher;

trait EmptyQueueableEventTrait
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
