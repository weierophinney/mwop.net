<?php

declare(strict_types=1);

namespace Mwop\Mastodon;

final class Status
{
    public const VISIBILITY_DIRECT   = 'direct';
    public const VISIBILITY_PRIVATE  = 'private';
    public const VISIBILITY_PUBLIC   = 'public';
    public const VISIBILITY_UNLISTED = 'unlisted';

    public function __construct(
        public readonly string $status,
        public readonly Visibility $visibility = Visibility::PUBLIC,
        public readonly string $language = 'en',
        public readonly ?string $idempotencyKey = null,
        public readonly ?array $mediaIds = null,
    ) {
    }
}
