<?php
namespace mwop\Mvc\Presentation\Helper;

use mwop\Stdlib\Route as Router;

class Url
{
    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function assemble(array $params = array(), array $options= array())
    {
        return $this->router->assemble($params, $options);
    }
}
