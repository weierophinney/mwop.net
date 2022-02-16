<?php

declare(strict_types=1);

namespace Mwop\Art;

class PhotoSearchResult
{
    public function __construct(
        public readonly string $filename,
        public readonly string $description,
    ) {
    }
}
