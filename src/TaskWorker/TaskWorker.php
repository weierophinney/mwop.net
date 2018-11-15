<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\TaskWorker;

use Phly\EventEmitter\Exception\ExceptionAggregate;
use Psr\EventDispatcher\MessageInterface;
use Psr\EventDispatcher\MessageNotifierInterface;
use Psr\Log\LoggerInterface;
use Swoole\Http\Server as HttpServer;
use Throwable;

class TaskWorker
{
    /** @var MessageNotifierInterface */
    private $notifier;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger, MessageNotifierInterface $notifier)
    {
        $this->logger = $logger;
        $this->notifier = $notifier;
    }

    public function __invoke(HttpServer $server, int $taskId, int $fromId, $data) : void
    {
        if (! $data instanceof MessageInterface) {
            $this->logger->error('Invalid data type provided to task worker: {type}', [
                'type' => is_object($data) ? get_class($data) : gettype($data)
            ]);
            return;
        }

        $this->logger->notice('Starting work on task {taskId} using data: {data}', [
            'taskId' => $taskId,
            'data'   => json_encode($data),
        ]);

        try {
            $this->notifier->notify($data);
        } catch (ExceptionAggregate $aggregate) {
            $this->logExceptionAggregate($aggregate, $taskId);
        } catch (Throwable $e) {
            $this->logNotifierException($e, $taskId);
        } finally {
            // Notify the server that processing of the task has finished:
            $server->finish('');
        }
    }

    private function logNotifierException(Throwable $e, int $taskId)
    {
        $this->logger->error('Error processing task {taskId}: {error}', [
            'taskId' => $taskId,
            'error'  => $this->formatExceptionForLogging($e),
        ]);
    }

    private function logExceptionAggregate(ExceptionAggregate $aggregate, int $taskId)
    {
        foreach ($aggregate->getListenerExceptions() as $index => $e) {
            $this->logIndividualException($e, $taskId, $index);
        }
    }

    private function logIndividualException(Throwable $e, int $taskId, int $index)
    {
        $this->logger->error('Error processing task {taskId} via listener {index}: {error}', [
            'taskId' => $taskId,
            'error'  => $this->formatExceptionForLogging($e),
            'index'  => $index,
        ]);
    }

    private function formatExceptionForLogging(Throwable $e) : string
    {
        return sprintf(
            "[%s - %d] %s\n%s",
            get_class($e),
            $e->getCode(),
            $e->getMessage(),
            $e->getTraceAsString()
        );
    }
}
