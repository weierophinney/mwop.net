<?php
namespace Mwop;

use Zend\Stratigility\Http\Request as ConduitRequest;
use Zend\Stratigility\MiddlewarePipe as Middleware;
use Phly\Mustache\Mustache;
use Psr\Http\Message\ServerRequestInterface as Request;
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

        return parent::__invoke($request->withAttribute('view', (object) [
            'template' => null,
            'model'    => [],
        ]), $response, $next);
    }

    public function render(Request $request, Response $response, callable $next)
    {
        if ($response->isComplete()) {
            return $response;
        }

        $view = $request->getAttribute('view', false);

        if (false === $view || ! $view->template) {
            return $next($request, $response);
        }

        return $response->write($this->renderer->render(
            $view->template,
            $view->model
        ));
    }

    private function injectViewMiddleware()
    {
        if ($this->activated) {
            return;
        }

        // Done in a closure due to how Zend\Stratigility\Utils::getArity works
        $this->pipe(function ($req, $res, $next) {
            return $this->render($req, $res, $next);
        });

        $this->activated = true;
    }
}
