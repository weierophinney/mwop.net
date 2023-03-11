<?php

declare(strict_types=1);

namespace Mwop\App\EventDispatcher;

use Phly\RedisTaskQueue\RedisTaskQueue;
use Psr\Log\LoggerInterface;

final class DeferredEventListener
{
    public function __construct(
        private RedisTaskQueue $queue,
        private ?LoggerInterface $logger = null,
    ) {
    }

    public function __invoke(DeferredEvent $event): void
    {
        $this->logger?->info('Queuing task of type {task}', [
            'task' => $event::class,
        ]);
        $this->queue->queue($event->wrappedEvent);
    }
}
