<?php

declare(strict_types=1);

namespace Mwop\App\EventDispatcher;

use JsonSerializable;

interface QueueableEvent extends JsonSerializable
{
    public static function fromJSON(string $json): self;

    public static function fromDataArray(array $data): self;
}
