<?php

declare(strict_types=1);

namespace Mwop\Cron;

use DateTimeInterface;

trait CronEventTrait
{
    private DateTimeInterface $timestamp;

    public static function forTimestamp(DateTimeInterface $timestamp): self
    {
        $instance            = new self();
        $instance->timestamp = $timestamp;
        return $instance;
    }

    public function timestamp(): DateTimeInterface
    {
        return $this->timestamp;
    }
}
