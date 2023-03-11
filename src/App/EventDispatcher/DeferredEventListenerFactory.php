<?php

declare(strict_types=1);

namespace Mwop\App\EventDispatcher;

use Phly\RedisTaskQueue\RedisTaskQueue;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

use function assert;

final class DeferredEventListenerFactory
{
    public function __invoke(ContainerInterface $container): DeferredEventListener
    {
        $queue = $container->get(RedisTaskQueue::class);
        assert($queue instanceof RedisTaskQueue);

        return new DeferredEventListener(
            $queue,
            $this->getLogger($container),
        );
    }

    private function getLogger(ContainerInterface $container): ?LoggerInterface
    {
        if (! $container->has(LoggerInterface::class)) {
            return null;
        }

        $logger = $container->get(LoggerInterface::class);
        assert($logger instanceof LoggerInterface);

        return $logger;
    }
}
