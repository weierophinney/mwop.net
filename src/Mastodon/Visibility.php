<?php

declare(strict_types=1);

namespace Mwop\Mastodon;

enum Visibility: string
{
    case DIRECT   = 'direct';
    case PRIVATE  = 'private';
    case PUBLIC   = 'public';
    case UNLISTED = 'unlisted';
}
