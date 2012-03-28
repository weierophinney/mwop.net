<?php
namespace PhlyCommon\Resource;

use PhlyCommon\ResourceCollection,
    PhlyCommon\Entity,
    Iterator;

class Collection implements ResourceCollection
{
    protected $count;
    protected $items;
    protected $class;
    protected $objects = array();

    public function __construct($items, $class)
    {
        $this->items = $items;
        $this->class = $class;
        $this->count = count($items);
    }

    public function count()
    {
        return $this->count;
    }

    public function current()
    {
        $item = current($this->items);
        if ($item === false) {
            return false;
        }
        $key  = $this->key();
        if (!isset($this->objects[$key])) {
            $object = new $this->class();
            $object->fromArray($item);
            $this->objects[$key] = $object;
        }
        return $this->objects[$key];
    }

    public function key()
    {
        return key($this->items);
    }

    public function next()
    {
        return next($this->items);
    }

    public function valid()
    {
        return ($this->current() !== false);
    }

    public function rewind()
    {
        reset($this->items);
    }

    /**
     * Cast collection to multi-dimensional array
     * 
     * @return array
     */
    public function toArray()
    {
        $items = array();
        foreach ($this as $key => $value) {
            $items[$key] = $value->toArray();
        }
        return $items;
    }

    /**
     * Populate from an array
     * 
     * @param  array $collection 
     * @return Collection
     */
    public function fromArray(array $collection)
    {
        $this->items = $collection;
        $this->count = count($collection);
        return $this;
    }
}
