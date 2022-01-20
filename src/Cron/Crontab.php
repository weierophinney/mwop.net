<?php

declare(strict_types=1);

namespace Mwop\Cron;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

use function count;

class Crontab implements Countable, IteratorAggregate
{
    /** @var Cronjob[] */
    private array $jobs = [];

    public function count(): int
    {
        return count($this->jobs);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->jobs);
    }

    public function append(Cronjob $job): void
    {
        $this->jobs[] = $job;
    }
}
