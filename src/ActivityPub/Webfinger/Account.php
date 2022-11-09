<?php

declare(strict_types=1);

namespace Mwop\ActivityPub\Webfinger;

use JsonSerializable;
use Psr\Link\EvolvableLinkProviderInterface;

// phpcs:ignore WebimpressCodingStandard.NamingConventions.Interface.Suffix
interface Account extends EvolvableLinkProviderInterface, JsonSerializable
{
    /** @psalm-return iterable<array-key, string> */
    public function getAliases(): iterable;

    /** @psalm-return non-empty string */
    public function getSubject(): string;
}
