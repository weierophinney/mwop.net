<?php

declare(strict_types=1);

namespace Mwop\Github\Webhook;

use Mwop\App\EventDispatcher\ArrayQueueableEventTrait;
use Mwop\App\EventDispatcher\QueueableEvent;

class Payload implements QueueableEvent
{
    use ArrayQueueableEventTrait;
}
