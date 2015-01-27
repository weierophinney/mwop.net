<?php
namespace Mwop\Auth;

use Phly\Conduit\MiddlewarePipe as BaseMiddleware;

class Middleware extends BaseMiddleware
{
    public function __construct(Auth $auth, AuthCallback $callback, Logout $logout)
    {
        parent::__construct();
        $this->pipe('/callback', $callback);
        $this->pipe('/github', $auth);
        $this->pipe('/google', $auth);
        $this->pipe('/twitter', $auth);
        $this->pipe('/logout', $logout);
    }
}
