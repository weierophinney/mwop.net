<?php

declare(strict_types=1);

namespace Mwop\Blog\Mapper;

use Laminas\Paginator\Paginator;
use Laminas\Tag\Cloud;
use Mwop\Blog\BlogPost;

interface MapperInterface
{
    public function fetch(string $id): ?BlogPost;

    public function fetchAll(): Paginator;

    public function fetchAllByAuthor(string $author): Paginator;

    public function fetchAllByTag(string $tag): Paginator;

    public function fetchTagCloud(): Cloud;

    public function search(string $toMatch): ?array;
}
