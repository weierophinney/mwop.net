<?php
namespace mwop\Stdlib;

interface Query extends ArraySerializable
{
    public function where($key, $comparison, $value = null);
    public function orWhere($key, $comparison, $value = null);
    public function limit($count, $offset = 0);
    public function getWhereClauses();
    public function getLimit();
    public function getOffset();
    public function sort($key, $direction = 'ASC');
}
