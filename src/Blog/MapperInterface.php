<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

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
