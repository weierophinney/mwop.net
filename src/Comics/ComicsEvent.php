<?php

declare(strict_types=1);

namespace Mwop\Comics;

use Mwop\Cron\CronEventInterface;
use Mwop\Cron\CronEventTrait;

class ComicsEvent implements CronEventInterface
{
    use CronEventTrait;
}
