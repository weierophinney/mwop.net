<?php

declare(strict_types=1);

namespace Mwop\ZendHQ\Console;

use Psr\Container\ContainerInterface;
use ZendHQ\JobQueue\JobQueue;

use function assert;
use function is_array;
use function is_string;

class SetupRecurringJobsFactory
{
    public function __invoke(ContainerInterface $container): SetupRecurringJobs
    {
        $jq = $container->get(JobQueue::class);
        assert($jq instanceof JobQueue);

        $config = $container->get('config');
        assert(is_array($config));

        $workerUrl = $config['jq']['workerUrl'] ?? '';
        assert(is_string($workerUrl) && ! empty($workerUrl));

        return new SetupRecurringJobs($jq, $workerUrl);
    }
}
