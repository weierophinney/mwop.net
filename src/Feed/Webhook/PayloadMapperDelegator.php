<?php

declare(strict_types=1);

namespace Mwop\Feed\Webhook;

use Mwop\App\EventDispatcher\PayloadMapper;
use Phly\RedisTaskQueue\Mapper\Mapper;
use Psr\Container\ContainerInterface;

use function assert;

final class PayloadMapperDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        callable $factory,
    ): Mapper {
        $mapper = $factory();
        assert($mapper instanceof Mapper);

        $mapper->attach(new PayloadMapper(Payload::class));
        return $mapper;
    }
}
