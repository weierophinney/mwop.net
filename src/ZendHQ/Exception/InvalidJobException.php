<?php

declare(strict_types=1);

namespace Mwop\ZendHQ\Exception;

use Mezzio\ProblemDetails\Exception\CommonProblemDetailsExceptionTrait;
use Mezzio\ProblemDetails\Exception\ProblemDetailsExceptionInterface;
use Mwop\App\EventDispatcher\QueueableEvent;
use RuntimeException;

use function sprintf;

class InvalidJobException extends RuntimeException implements ProblemDetailsExceptionInterface
{
    use CommonProblemDetailsExceptionTrait;

    public function __construct(string $message, string $title, string $type, array $job)
    {
        parent::__construct($message, 400);

        $this->detail            = $message;
        $this->status            = 400;
        $this->title             = $title;
        $this->type              = $type;
        $this->additional['job'] = $job;
    }

    public static function forMissingJobType(array $job): self
    {
        return new self(
            'Invalid job; missing job "type"',
            'Missing job type',
            'http://worker/error/job/type/missing',
            $job
        );
    }

    public static function forInvalidJobType(array $job): self
    {
        return new self(
            sprintf(
                'Invalid job; job "type" must be a valid event class name implementing %s',
                QueueableEvent::class,
            ),
            'Invalid job type',
            'http://worker/error/job/type/malformed',
            $job
        );
    }

    public static function forInvalidJobData(array $job): self
    {
        return new self(
            'Invalid job; job "data" must be an object',
            'Invalid job data',
            'http://worker/error/job/data/malformed',
            $job
        );
    }
}
