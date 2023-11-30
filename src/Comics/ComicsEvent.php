<?php

declare(strict_types=1);

namespace Mwop\Comics;

use Mwop\App\EventDispatcher\EmptyQueueableEventTrait;
use Mwop\App\EventDispatcher\QueueableEvent;

class ComicsEvent implements QueueableEvent
{
    use EmptyQueueableEventTrait;
}
