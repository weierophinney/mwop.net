<?php

declare(strict_types=1);

namespace Mwop\Mastodon;

// phpcs:ignore WebimpressCodingStandard.NamingConventions.Interface.Suffix
interface ApiClient
{
    public function createStatus(Credentials $credentials, Status $status): ApiResult;

    public function uploadMedia(
        Credentials $credentials,
        Media $media,
        ?string $description = null,
    ): ApiResult;
}
