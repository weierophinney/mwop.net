<?php

declare(strict_types=1);

namespace Mwop\ZendHQ;

class EventFactory
{
    public function __invoke(object $job): object
    {
        return new ($job->type)($job->data ?? null);
    }
}
