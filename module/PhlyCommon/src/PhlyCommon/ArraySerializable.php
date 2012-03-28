<?php
namespace PhlyCommon;

interface ArraySerializable
{
    /**
     * Cast object to array
     * 
     * @return array
     */
    public function toArray();

    /**
     * Populate object from array
     * 
     * @param  array $array 
     * @return ArraySerializable
     */
    public function fromArray(array $array);
}
