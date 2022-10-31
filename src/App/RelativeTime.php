<?php

namespace Mwop\App;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;

class RelativeTime
{
    public function __invoke(DateTimeInterface $compare): string
    {
        $now = new DateTimeImmutable();
        $diff = $now->diff($compare);
        
        return $diff->invert === 1
            ? $this->preparePastTimeString($diff)
            : $this->prepareFutureTimeString($diff);
    }

    private function preparePastTimeString(DateInterval $diff): string
    {
        if ($diff->y > 0 && $diff->m >= 6) {
            return $diff->y === 1 ? 'more than a year ago' : "{$diff->y} years ago";
        }

        if ($diff->y === 1 && $diff->m < 6) {
            return 'a year ago';
        }

        if ($diff->m > 0) {
            return $diff->m === 1 ? '1 month ago' : "{$diff->m} months ago";
        }

        if ($diff->d > 1) {
            return "{$diff->d} days ago";
        }

        if ($diff->d === 1) {
            return "a day ago";
        }

        if ($diff->h > 1) {
            return "{$diff->h} hours ago";
        }

        if ($diff->h === 1) {
            return "around an hour ago";
        }

        if ($diff->i > 1) {
            return "{$diff->i} minutes ago";
        }

        if ($diff->i === 1) {
            return "around a minute ago";
        }

        if ($diff->s > 1) {
            return "{$diff->s} seconds ago";
        }

        return "just now";
    }

    private function prepareFutureTimeString(DateInterval $diff): string
    {
        if ($diff->y > 0 && $diff->m >= 6) {
            return $diff->y === 1 ? 'more than a year from now' : "{$diff->y} years from now";
        }

        if ($diff->y === 1 && $diff->m < 6) {
            return 'a year from now';
        }

        if ($diff->m > 0) {
            return $diff->m === 1 ? '1 month from now' : "{$diff->m} months from now";
        }

        if ($diff->d > 1) {
            return "{$diff->d} days from now";
        }

        if ($diff->d === 1) {
            return "a day from now";
        }

        if ($diff->h > 1) {
            return "{$diff->h} hours from now";
        }

        if ($diff->h === 1) {
            return "around an hour from now";
        }

        if ($diff->i > 1) {
            return "{$diff->i} minutes from now";
        }

        if ($diff->i === 1) {
            return "around a minute from now";
        }

        if ($diff->s > 1) {
            return "{$diff->s} seconds from now";
        }

        return "now";
    }
}
