<?php
namespace mwop\Stdlib;

use Zend\Filter\InputFilter;

interface Validatible
{
    public static function getDefaultInputFilter();
    public function setInputFilter(InputFilter $filter);
    public function getInputFilter();
    public function isValid();
}
