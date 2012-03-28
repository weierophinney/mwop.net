<?php
namespace PhlyCommon;

use Countable,
    Iterator;

interface ResourceCollection extends Countable, Iterator, ArraySerializable
{
    public function __construct($items, $entityClass);
}
