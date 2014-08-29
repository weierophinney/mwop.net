<?php
namespace Mwop\Blog;

interface MapperInterface
{
    public function fetch($id);

    public function fetchAll();

    public function fetchAllByAuthor($author);

    public function fetchAllByTag($tag);
}
