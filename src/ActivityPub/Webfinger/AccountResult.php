<?php

declare(strict_types=1);

namespace Mwop\ActivityPub\Webfinger;

// phpcs:ignore WebimpressCodingStandard.NamingConventions.Interface.Suffix
interface AccountResult
{
    public function getStatus(): int;

    public function getContentType(): string;

    public function getContent(): string;
}
