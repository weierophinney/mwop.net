<?php
namespace Mwop;

use Phly\Conduit\Http\Request as ConduitRequest;
use Phly\Conduit\Middleware;
use Phly\Mustache\Mustache;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Templated extends Middleware
{
    private $activated = false;
    private $renderer;

    public function __construct(Mustache $renderer)
    {
        parent::__construct();
        $this->renderer = $renderer;
    }

    public function __invoke(Request $request, Response $response, callable $next = null)
    {
        // Inject rendering middleware; done now to ensure it triggers last.
        $this->injectViewMiddleware();

        if (! $request instanceof ConduitRequest) {
            $request = new ConduitRequest($request);
        }

        $request->view = (object) [
            'template' => null,
            'model'    => [],
        ];

        parent::__invoke($request, $response, $next);
    }

    public function render(Request $request, Response $response, callable $next)
    {
        if ($response->isComplete()) {
            return;
        }

        if (! $request->view || ! $request->view->template) {
            return $next();
        }

        $response->write($this->renderer->render(
            $request->view->template,
            $request->view->model
        ));
    }

    private function injectViewMiddleware()
    {
        if ($this->activated) {
            return;
        }

        // Done in a closure due to how Phly\Conduit\Utils::getArity works 
        $this->pipe(function ($req, $res, $next) {
            return $this->render($req, $res, $next);
        });

        $this->activated = true;
    }
}
