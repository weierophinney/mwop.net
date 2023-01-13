<?php

declare(strict_types=1);

namespace Mwop\Mastodon;

// phpcs:ignore WebimpressCodingStandard.NamingConventions.Interface.Suffix
interface ApiClient
{
    public function authenticate(string $clientId, string $clientSecret): Authorization;

    public function createStatus(Authorization $auth, Status $status): ApiResult;

    public function uploadMedia(
        Authorization $auth,
        Media $media,
        ?string $description = null,
        ?Media $thumbnail
    ): ApiResult;
}
