<?php
namespace Mwop\Contact;

use Phly\Conduit\MiddlewarePipe as BaseMiddleware;

class Middleware extends BaseMiddleware
{
    public function __construct(callable $landingPage, callable $handler, callable $thankYouPage)
    {
        parent::__construct();
        $this->pipe('/', $landingPage);
        $this->pipe('/process', $handler);
        $this->pipe('/thank-you', $thankYouPage);
    }
}
