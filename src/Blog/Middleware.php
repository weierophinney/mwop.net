<?php
namespace Mwop\Blog;

use Phly\Conduit\Middleware as BaseMiddleware;

class Middleware extends BaseMiddleware
{
    public function __construct(EngineMiddleware $engine, CachingMiddleware $cache)
    {
        parent::__construct();

        $this->pipe($cache);
        $this->pipe($engine);
        $this->pipe($cache);
    }
}
