<?php

declare(strict_types=1);

namespace Mwop\Cron;

use Cron\CronExpression;
use Psr\Log\LoggerInterface;
use ReflectionClass;

use function array_key_exists;
use function class_exists;
use function is_array;
use function is_string;
use function sprintf;

/**
 * @internal
 */
class ConfigParser
{
    public function __invoke(array $jobs, LoggerInterface $logger): Crontab
    {
        $crontab = new Crontab();

        foreach ($jobs as $index => $jobDetails) {
            $cronjob = $this->validateAndExtractCronjob($jobDetails, $index, $logger);

            if (null === $cronjob) {
                continue;
            }

            $crontab->append($cronjob);
        }

        return $crontab;
    }

    private function validateAndExtractCronjob(mixed $jobDetails, int|string $index, LoggerInterface $logger): ?Cronjob
    {
        if (! is_array($jobDetails)) {
            $logger->warning(sprintf(
                'Job at index %s is invalid; it must be an array with the keys "schedule" and "event"',
                (string) $index,
            ));
            return null;
        }

        if (! array_key_exists('schedule', $jobDetails)) {
            $logger->warning(sprintf(
                'Job at index %s is invalid; missing "schedule" key',
                (string) $index,
            ));
            return null;
        }

        if (! $this->isScheduleValid($jobDetails['schedule'], $index, $logger)) {
            return null;
        }

        if (! array_key_exists('event', $jobDetails)) {
            $logger->warning(sprintf(
                'Job at index %s is invalid; missing "event" key',
                (string) $index,
            ));
            return null;
        }

        if (! $this->isEventClassValid($jobDetails['event'], $index, $logger)) {
            return null;
        }

        return new Cronjob(
            schedule: $jobDetails['schedule'],
            eventClass: $jobDetails['event'],
        );
    }

    private function isScheduleValid(mixed $schedule, int|string $index, LoggerInterface $logger): bool
    {
        if (! is_string($schedule)) {
            $logger->warning(sprintf(
                'Job at index %s is invalid; "schedule" value is not a string',
                (string) $index,
            ));
            return false;
        }

        if (! CronExpression::isValidExpression($schedule)) {
            $logger->warning(sprintf(
                'Job at index %s is invalid; schedule "%s" is invalid',
                (string) $index,
                $schedule,
            ));
            return false;
        }

        return true;
    }

    private function isEventClassValid(mixed $eventClass, int|string $index, LoggerInterface $logger): bool
    {
        if (! is_string($eventClass)) {
            $logger->warning(sprintf(
                // phpcs:ignore Generic.Files.LineLength.TooLong
                'Job at index %s is invalid; non-string "event" key provided; must be a class name of a %s implementation',
                (string) $index,
                CronEventInterface::class,
            ));
            return false;
        }

        if (
            ! class_exists($eventClass)
            || ! $this->isCronEvent($eventClass)
        ) {
            $logger->warning(sprintf(
                // phpcs:ignore Generic.Files.LineLength.TooLong
                'Job at index %s is invalid; "event" value ("%s") must be a class name of a %s implementation',
                (string) $index,
                $eventClass,
                CronEventInterface::class,
            ));
            return false;
        }

        return true;
    }

    private function isCronEvent(string $eventClass): bool
    {
        $r = new ReflectionClass($eventClass);
        return $r->implementsInterface(CronEventInterface::class);
    }
}
