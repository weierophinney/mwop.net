<?php

declare(strict_types=1);

namespace Mwop\App\EventDispatcher;

use Phly\RedisTaskQueue\Mapper\MapperInterface;

final class PayloadMapper implements MapperInterface
{
    public function __construct(
        /** @psalm-var class-string<AbstractPayload> */
        private readonly string $className,
    ) {
    }

    public function handlesArray(array $serialized): bool
    {
        if (! array_key_exists('__type', $serialized)) {
            return false;
        }

        return $serialized['__type'] === $this->className;
    }

    public function handlesObject(object $object): bool
    {
        return $object instanceof $this->className;
    }

    public function castToArray(object $object): array
    {
        return [
            '__type'  => $this->className,
            'payload' => $object->payload,
        ];
    }

    public function castToObject(array $serialized): object
    {
        return new ($this->className)($serialized['payload']);
    }
}
