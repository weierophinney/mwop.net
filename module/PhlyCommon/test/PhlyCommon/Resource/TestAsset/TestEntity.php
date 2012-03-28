<?php

namespace PhlyCommon\Resource\TestAsset;

use PhlyCommon\Entity,
    Zend\Filter\InputFilter;

class TestEntity implements Entity
{
    protected $inputFilter;

    public function setInputFilter(InputFilter $filter)
    {
        $this->inputFilter = $filter;
    }

    public function getInputFilter()
    {
        return $this->inputFilter;
    }

    public function isValid()
    {
        return true;
    }

    public function fromArray(array $array)
    {
        foreach ($array as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function toArray()
    {
        return (array) $this;
    }
}
