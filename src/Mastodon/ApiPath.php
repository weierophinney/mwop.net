<?php

declare(strict_types=1);

namespace Mwop\Mastodon;

enum ApiPath: string
{
    case OAUTH  = '/oauth/token';
    case MEDIA  = '/api/v2/media';
    case STATUS = '/api/v1/status';
}
