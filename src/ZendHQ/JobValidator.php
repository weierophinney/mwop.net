<?php

declare(strict_types=1);

namespace Mwop\ZendHQ;

use Mwop\QueueableEvent;

class JobValidator
{
    /** @throws Exception\InvalidJobException */
    public function __invoke(array $job): void
    {
        if (! array_key_exists('type', $job)) {
            throw Exception\InvalidJobException::forMissingJobType($job);
        }

        if (! is_string($job['type'])
            || ! class_exists($job['type'])
            || ! is_subclass_of($job['type'], QueueableEvent::class)
        ) {
            throw Exception\InvalidJobException::forInvalidJobType($job);
        }

        if (array_key_exists('data', $job) && ! is_array($job['data'])) {
            throw Exception\InvalidJobException::forInvalidJobData($job);
        }
    }
}
