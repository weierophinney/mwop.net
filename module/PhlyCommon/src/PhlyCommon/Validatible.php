<?php
namespace PhlyCommon;

use Zend\Filter\InputFilter;

interface Validatible
{
    public function setInputFilter(InputFilter $filter);
    public function getInputFilter();
    public function isValid();
}
