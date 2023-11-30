<?php

declare(strict_types=1);

namespace Mwop\App\Factory;

use Mezzio\Application;
use Mwop\Comics\ComicsEvent;
use Mwop\Mastodon\PostEvent;
use Psr\Container\ContainerInterface;
use ZendHQ\JobQueue\HTTPJob;
use ZendHQ\JobQueue\JobQueue;
use ZendHQ\JobQueue\Queue;
use ZendHQ\JobQueue\RecurringSchedule;

use function assert;
use function count;
use function is_array;
use function is_string;
use function json_encode;

class CronjobDelegator
{
    public function __invoke(ContainerInterface $container, string $name, callable $factory): Application
    {
        /** @var Application $app */
        $app = $factory();

        $jq = $container->get(JobQueue::class);
        assert($jq instanceof JobQueue);

        $config = $container->get('config');
        assert(is_array($config));

        $workerUrl = $config['jq']['workerUrl'] ?? '';
        assert(is_string($workerUrl) && ! empty($workerUrl));

        $this->registerComicsCronjob($jq, $workerUrl);
        $this->registerMastodonCronjob($jq, $workerUrl);

        return $app;
    }

    /** @psalm-param non-empty-string $workerUrl */
    private function registerComicsCronjob(JobQueue $jq, string $workerUrl): void
    {
        $queue = $this->getQueue($jq, 'comics');
        $jobs  = $queue->getJobsByName('comics');
        if (count($jobs) !== 0) {
            return;
        }

        $job = new HTTPJob($workerUrl, HTTPJob::HTTP_METHOD_POST);
        $job->setName('comics');
        $job->addHeader('Content-Type', 'application/mwop-net-jq+json');
        $job->setRawBody(json_encode([
            'type' => ComicsEvent::class,
            'data' => [],
        ]));
        $queue->scheduleJob($job, new RecurringSchedule('0 0 */3 * * *'));
    }

    /** @psalm-param non-empty-string $workerUrl */
    private function registerMastodonCronjob(JobQueue $jq, string $workerUrl): void
    {
        $queue = $this->getQueue($jq, 'mastodon');
        $jobs  = $queue->getJobsByName('mastodon');
        if (count($jobs) !== 0) {
            return;
        }

        $job = new HTTPJob($workerUrl, HTTPJob::HTTP_METHOD_POST);
        $job->setName('mastodon');
        $job->addHeader('Content-Type', 'application/mwop-net-jq+json');
        $job->setRawBody(json_encode([
            'type' => PostEvent::class,
            'data' => [],
        ]));
        $queue->scheduleJob($job, new RecurringSchedule('0 */15 * * * *'));
    }

    private function getQueue(JobQueue $jq, string $name): Queue
    {
        return $jq->hasQueue($name)
            ? $jq->getQueue($name)
            : $jq->addQueue($name, (new CronjobQueueDefinitionFactory())());
    }
}
