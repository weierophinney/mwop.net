<?php
namespace mwop\Stdlib;

use Fig\Request;

interface Route
{
    public function setRequest(Request $request); 
    public function match(Request $request); 
    public function assemble(array $params = array(), array $options = array());
}
