<?php
namespace PhlyCommon\Resource;

use PhlyCommon\ResourceCollection,
    MongoCursor;

class MongoCollection implements ResourceCollection
{
    protected $count;
    protected $items;
    protected $class;
    protected $objects = array();

    public function __construct($items, $class)
    {
        if (!$items instanceof MongoCursor) {
            throw new \InvalidArgumentException(sprintf(
                '%s expects a MongoCursor; received "%s"',
                __CLASS__,
                (is_object($items) ? get_class($items) : gettype($items))
            ));
        }

        $this->items = $items;
        $this->count = $items->count(true);
        $this->class = $class;
    }

    public function count()
    {
        return $this->count;
    }

    public function current()
    {
        if (!$item = $this->items->current()) {
            return false;
        }


        $key  = $this->key();
        if (!isset($this->objects[$key])) {
            // Normalize "id" field
            if (array_key_exists('_id', $item)) {
                $item['id'] = (string) $item['_id'];
                unset($item['_id']);
            }
            $object = new $this->class();
            $object->fromArray($item);
            $this->objects[$key] = $object;
        }
        return $this->objects[$key];
    }

    public function key()
    {
        return $this->items->key();
    }

    public function next()
    {
        return $this->items->next();
    }

    public function valid()
    {
        return ($this->current() !== false);
    }

    public function rewind()
    {
        $this->items->rewind();
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
