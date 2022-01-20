<?php

declare(strict_types=1);

namespace Mwop\Cron;

use DateTimeInterface;

interface CronEventInterface
{
    public static function forTimestamp(DateTimeInterface $timestamp): self;

    public function timestamp(): DateTimeInterface;
}
