<?php
namespace mwop\Stdlib;

use Zend\Stdlib\SplQueue;

class UniqueFilteringIterator extends SplQueue
{
    protected $cached = array();

    public function offsetSet($offset, $value)
    {
        if (!$this->add($value)) {
            return;
        }
        return parent::offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $value = parent::offsetGet($offset);
        $this->remove($value);
        return parent::offsetUnset($offset);
    }

    public function push($value)
    {
        if (!$this->add($value)) {
            return;
        }
        return parent::push($value);
    }

    public function pop()
    {
        $value = parent::pop();
        $this->remove($value);
        return $value;
    }

    public function unshift($value)
    {
        if (!$this->add($value)) {
            return;
        }
        return parent::unshift($value);
    }

    public function shift()
    {
        $value = parent::shift();
        $this->remove($value);
        return $value;
    }

    protected function add($item)
    {
        if (in_array($item, $this->cached, true)) {
            return false;
        }

        $this->cached[] = $item;
        return true;
    }

    protected function remove($value)
    {
        $i     = array_search($value, $this->cached, true);
        if ($i !== false) {
            unset($this->cached[$i]);
        }
    }
}
