<?php
namespace Mwop;

class QueryParams
{
    private $params;

    public function __construct(array $params = null)
    {
        if (null === $params) {
            $params = $_GET;
        }

        $this->params = $params;
    }

    public function __invoke($req, $res, $next)
    {
        $req->query = $this->params;
        $next();
    }
}
