<?php

declare(strict_types=1);

namespace Mwop\Cron;

class Cronjob
{
    public function __construct(
        public readonly string $schedule,
        public readonly string $eventClass,
    ) {
    }
}
