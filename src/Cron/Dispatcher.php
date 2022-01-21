<?php

declare(strict_types=1);

namespace Mwop\Cron;

use Cron\CronExpression;
use DateTimeImmutable;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

use function sprintf;
use function vsprintf;

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
            $this->log('Evaluating "%s %s"', $job->schedule, $job->eventClass);
            $cron = new CronExpression($job->schedule);
            if (! $cron->isDue($now)) {
                $this->log('- Not due; skipping');
                continue;
            }

            $this->log('- Due! dispatching %s', $job->eventClass);
            $this->eventDispatcher->dispatch(($job->eventClass)::forTimestamp($now));
        }
    }

    private function log(string $message, string ...$replacements): void
    {
        $message = '[CRON] ' . $message;
        $this->logger->info(vsprintf($message, $replacements));
    }
}
