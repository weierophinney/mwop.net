<?php
namespace Mwop\User;

use Phly\Conduit\Middleware as BaseMiddleware;

class Middleware extends BaseMiddleware
{
    public function __construct(Auth $auth, AuthCallback $callback, Logout $logout)
    {
        parent::__construct();
        $this->pipe('/', $auth);
        $this->pipe('/github', $auth);
        $this->pipe('/google', $auth);
        $this->pipe('/twitter', $auth);
        $this->pipe('/callback', $callback);
        $this->pipe('/logout', $logout);
    }
}
