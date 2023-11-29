<?php

declare(strict_types=1);

namespace Mwop\ZendHQ;

class JobValidator
{
    /** @throws Exception\InvalidJobException */
    public function __invoke(object $job): void
    {
        if (! isset($job->type)) {
            throw Exception\InvalidJobException::forMissingJobType($job);
        }

        if (! is_string($job->type) || ! class_exists($job->type)) {
            throw Exception\InvalidJobException::forInvalidJobType($job);
        }

        if (isset($job->data) && ! is_object($job->data)) {
            throw Exception\InvalidJobException::forInvalidJobData($job);
        }
    }
}
