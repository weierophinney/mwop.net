<?php
namespace Mwop\Blog;

use Zend\Paginator\Paginator;
use Zend\Tag\Cloud;

interface MapperInterface
{
    public function fetch(string $id) : array;

    public function fetchAll() : Paginator;

    public function fetchAllByAuthor(string $author) : Paginator;

    public function fetchAllByTag(string $tag) : Paginator;

    public function fetchTagCloud() : Cloud;
}
