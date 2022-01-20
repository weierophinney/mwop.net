<?php

declare(strict_types=1);

namespace Mwop\Cron;

use Cron\CronExpression;
use DateTimeImmutable;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

use function sprintf;

class Dispatcher
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private Crontab $crontab,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(): void
    {
        $this->logger->info(sprintf('%s invoked', self::class));
        $now = new DateTimeImmutable();
        foreach ($this->crontab as $job) {
            $this->logger->info(sprintf('Evaluating "%s %s"', $job->schedule, $job->eventClass));
            $cron = new CronExpression($job->schedule);
            if (! $cron->isDue($now)) {
                $this->logger->info(sprintf('- Not due; skipping'));
                continue;
            }

            $this->logger->info(sprintf('- Due! dispatching %s', $job->eventClass));
            $this->eventDispatcher->dispatch(($job->eventClass)::forTimestamp($now));
        }
    }
}
