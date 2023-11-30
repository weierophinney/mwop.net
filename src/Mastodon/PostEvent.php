<?php

declare(strict_types=1);

namespace Mwop\Mastodon;

use Mwop\App\EventDispatcher\EmptyQueueableEventTrait;
use Mwop\App\EventDispatcher\QueueableEvent;

class PostEvent implements QueueableEvent
{
    use EmptyQueueableEventTrait;
}
