<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\TaskWorker;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Swoole\Http\Server as HttpServer;
use Throwable;

class TaskWorker
{
    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger, EventDispatcherInterface $dispatcher)
    {
        $this->logger     = $logger;
        $this->dispatcher = $dispatcher;
    }

    public function __invoke(HttpServer $server, int $taskId, int $fromId, $data) : void
    {
        if (! is_object($data)) {
            $this->logger->error('Invalid data type provided to task worker: {type}', [
                'type' => gettype($data)
            ]);
            return;
        }

        $this->logger->notice('Starting work on task {taskId} using data: {data}', [
            'taskId' => $taskId,
            'data'   => json_encode($data),
        ]);

        try {
            $this->dispatcher->dispatch($data);
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
