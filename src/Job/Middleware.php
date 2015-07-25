<?php
namespace Mwop\Job;

use Zend\Stratigility\MiddlewarePipe as BaseMiddleware;

class Middleware extends BaseMiddleware
{
    public function __construct()
    {
        parent::__construct();

        $this->pipe('/clear-cache', new ClearCache());
        $this->pipe('/comics', new Comics());
        $this->pipe('/github-feed', new GithubFeed());
    }
}
