<?php
namespace mwop\Stdlib;

use Countable,
    Iterator;

interface ResourceCollection extends Countable, Iterator
{
    public function __construct($items, $entityClass);
}
