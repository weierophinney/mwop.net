<?php

declare(strict_types=1);

namespace Mwop\App\EventDispatcher;

use Psr\Log\LoggerInterface;
use ZendHQ\JobQueue\HTTPJob;
use ZendHQ\JobQueue\JobQueue;
use ZendHQ\JobQueue\Queue;
use ZendHQ\JobQueue\QueueDefinition;

use function explode;
use function implode;
use function json_encode;
use function strtolower;

final class DeferredEventListener
{
    public function __construct(
        private string $workerUrl,
        private JobQueue $jq,
        private QueueDefinition $queueDefinition,
        private ?LoggerInterface $logger = null,
    ) {
    }

    public function __invoke(DeferredEvent $event): void
    {
        $baseEvent = $event->wrappedEvent;

        $this->logger?->info('Queuing task of type {task}', [
            'task' => $baseEvent::class,
        ]);

        $queue = $this->getQueue($baseEvent::class);

        $job = new HTTPJob($this->workerUrl, HTTPJob::HTTP_METHOD_POST);
        $job->addHeader('Content-Type', 'application/mwop-net-jq+json');
        $job->setRawBody(json_encode([
            'type' => $baseEvent::class,
            'data' => $baseEvent,
        ]));

        $queue->scheduleJob($job);
    }

    private function getQueue(string $class): Queue
    {
        $queueName = $this->deriveQueueNameFromEventClass($class);
        return $this->jq->hasQueue($queueName)
            ? $this->jq->getQueue($queueName)
            : $this->jq->addQueue($queueName, $this->queueDefinition);
    }

    private function deriveQueueNameFromEventClass(string $class): string
    {
        $parts = explode('\\', strtolower($class));
        return implode('-', $parts);
    }
}
