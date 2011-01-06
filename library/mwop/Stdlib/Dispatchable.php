<?php
namespace mwop\Stdlib;

use Fig\Request,
    Fig\Response;

interface Dispatchable
{
    public function dispatch(Request $request, Response $response = null);
}
