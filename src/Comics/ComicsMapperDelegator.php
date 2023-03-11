<?php

declare(strict_types=1);

namespace Mwop\Comics;

use Phly\RedisTaskQueue\Mapper\EmptyObjectMapper;
use Phly\RedisTaskQueue\Mapper\Mapper;
use Psr\Container\ContainerInterface;

final class ComicsMapperDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        callable $factory,
    ): Mapper{
        $mapper = $factory();
        assert($mapper instanceof Mapper);

        $mapper->attach(new EmptyObjectMapper(ComicsEvent::class));
        return $mapper;
    }
}
