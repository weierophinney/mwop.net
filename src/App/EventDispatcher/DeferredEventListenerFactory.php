<?php

declare(strict_types=1);

namespace Mwop\App\EventDispatcher;

use Mwop\App\Factory\DeferredJobQueueDefinitionFactory;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use ZendHQ\JobQueue\JobQueue;

use function assert;
use function is_string;

final class DeferredEventListenerFactory
{
    public function __invoke(ContainerInterface $container): DeferredEventListener
    {
        $jq = $container->get(JobQueue::class);
        assert($jq instanceof JobQueue);

        $config    = $container->get('config');
        $workerUrl = $config['jq']['workerUrl'] ?? '';
        assert(is_string($workerUrl) && ! empty($workerUrl));

        return new DeferredEventListener(
            $workerUrl,
            $jq,
            (new DeferredJobQueueDefinitionFactory())(),
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
