<?php
namespace mwop\Stdlib;

use Fig\Request;

interface ViewPresentation
{
    public function layout($view = null);
    public function helper($spec = null);
}
