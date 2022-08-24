<?php

declare(strict_types=1);

namespace Mwop\Art;

use CuyZ\Valinor\MapperBuilder;
use DateTimeImmutable;
use DateTimeInterface;
use Psr\Container\ContainerInterface;

use function preg_match;
use function sprintf;

class MapperBuilderDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $factory,
    ): MapperBuilder {
        /** @var MapperBuilder $builder */
        $builder = $factory();

        // phpcs:disable WebimpressCodingStandard.NamingConventions.ValidVariableName.NotCamelCaps
        // MapperBuilder is immutable; capture the return value.
        // This constructor is for incoming IG payloads
        $builder = $builder->registerConstructor(
            /**
             * @param non-empty-string $source_url
             * @param non-empty-string $created_at
             */
            fn (
                string $url,
                string $source_url,
                string $description,
                string $created_at,
            ): Photo => new Photo(
                $url,
                $source_url,
                $description,
                $this->transformStringDateTime($created_at)
            ),
        );
        // phpcs:enable WebimpressCodingStandard.NamingConventions.ValidVariableName.NotCamelCaps

        return $builder;
    }

    private function transformStringDateTime(string $dateTime): DateTimeInterface
    {
        $matches = [];

        if (
            ! preg_match(
                '/^(?P<month>\S+) (?P<day>\d+), (?P<year>\d{4}) at (?P<time>\d{2}:\d{2})(?P<meridian>am|pm)$/i',
                $dateTime,
                $matches
            )
        ) {
            return new DateTimeImmutable($dateTime);
        }

        return new DateTimeImmutable(sprintf(
            '%s %d, %d %s%s',
            $matches['month'],
            $matches['day'],
            $matches['year'],
            $matches['time'],
            $matches['meridian'],
        ));
    }
}
