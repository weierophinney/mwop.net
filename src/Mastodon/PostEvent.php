<?php

declare(strict_types=1);

namespace Mwop\Mastodon;

use Mwop\Cron\CronEventInterface;
use Mwop\Cron\CronEventTrait;

class PostEvent implements CronEventInterface
{
    use CronEventTrait;
}
