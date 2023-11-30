<?php

declare(strict_types=1);

namespace Mwop\Mastodon;

use Mwop\App\EventDispatcher\QueueableEvent;

class PostEvent implements QueueableEvent
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
