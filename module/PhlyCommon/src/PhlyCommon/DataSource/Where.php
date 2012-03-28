<?php
namespace PhlyCommon\DataSource;

class Where
{
    public $type;
    public $key;
    public $comparison;
    public $value;

    public function __construct($type, $key, $comparison, $value)
    {
        $type = strtoupper($type);
        if (!in_array($type, array('AND', 'OR'))) {
            throw new \InvalidArgumentException('Expected "AND" or "OR" for where clause type; received "' . $type . '"');
        }

        $this->type       = $type;
        $this->key        = $key;
        $this->comparison = $comparison;
        $this->value      = $value;
    }
}
