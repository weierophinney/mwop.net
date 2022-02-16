<?php

declare(strict_types=1);

namespace Mwop\Art;

use Illuminate\Support\Collection;
use Laminas\Paginator\Paginator;

interface PhotoMapper
{
    public function fetchAll(): Paginator;

    public function fetch(string $filename): ?Photo;

    public function search(string $toMatch): Collection

    public function create(Photo $photo): void;
}
