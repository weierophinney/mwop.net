<?php
namespace mwop\Controller;

use mwop\Stdlib\Dispatchable,
    Fig\Request,
    Fig\Response,
    Zend\Http\Request as HttpRequest,
    Zend\Http\Response as HttpResponse,
    Zend\EventManager\EventCollection,
    Zend\EventManager\EventManager,
    Zend\Di\ServiceLocation,
    mwop\Stdlib\RouteStack,
    mwop\Mvc\Router;

class Front implements Dispatchable
{
    protected $controllerMap = array();
    protected $events;
    protected $response;
    protected $router;
    protected $services;

    public function __construct(ServiceLocation $services)
    {
        $this->services = $services;
    }

    public function events(EventCollection $events = null)
    {
        if (null !== $events) {
            $this->events = $events;
        } elseif (null === $this->events) {
            if (!($events = $this->services->get('events'))) {
                $events = new EventManager(array(__CLASS__, get_called_class()));
            }
            $this->events = $events;
        }
        return $this->events;
    }

    public function router(RouteStack $router = null)
    {
        if (null !== $router) {
            $this->router = $router;
        } elseif (null === $this->router) {
            if (!($router = $this->services->get('router'))) {
                $router = new Router();
            }
            $this->router = $router;
        }
        return $this->router;
    }

    public function response(Response $response = null)
    {
        if (null !== $response) {
            $this->response = $response;
        } elseif (null === $this->response) {
            if (!($response = $this->services->get('response'))) {
                $response = new HttpResponse();
            }
            $this->response = $response;
        }
        return $this->response;
    }

    public function setControllerMap($map)
    {
        if (!is_array($map) && !$map instanceof \Traversable) {
            throw new \DomainException(sprintf(
                'Expected an array or Traversable; received "%s"',
                (is_object($map) ? get_class($map) : gettype($class))
            ));
        }
        foreach ($map as $name => $class) {
            $this->controllerMap[$name] = $class;
        }
        return $this;
    }
    
    public function addControllerMap($name, $class)
    {
        $this->controllerMap[$name] = $class;
        return $this;
    }

    public function dispatch(Request $request, Response $response = null)
    {
        if (null !== $response) {
            $this->response($response);
        }
        $response = $this->response();

        $responseComplete = function($result) {
            return ($result instanceof Response);
        };
        $params = compact('request', 'response');

        $result = $this->events()->triggerUntil(
            __FUNCTION__ . '.router.pre', $this, $params, $responseComplete
        );
        if ($result->stopped()) {
            return $result->last();
        }

        if (!$result = $this->router()->match($request)) {
            $this->prepare404();
            return $response;
        }
        $request->setMetadata($result);

        $result = $this->events()->triggerUntil(
            __FUNCTION__ . '.router.post', $this, $params, $responseComplete
        );
        if ($result->stopped()) {
            return $result->last();
        }

        if (!$controller = $request->getMetadata('controller', false)) {
            $this->prepare404();
            return $response;
        }

        if (!isset($this->controllerMap[$controller])) {
            $this->prepare404();
            return $response;
        }

        $controllerClass = $this->controllerMap[$controller];
        $dispatchable = $this->services->get($controllerClass);
        if (!$dispatchable instanceof Dispatchable) {
            throw new \DomainException(sprintf(
                'Invalid controller "%s" mapped; does not implement Dispatchable',
                $controllerClass
            ));
        }

        $result = $this->events()->triggerUntil(
            __FUNCTION__ . '.dispatch.pre', $this, $params, $responseComplete
        );
        if ($result->stopped()) {
            return $result->last();
        }

        $result = $dispatchable->dispatch($request, $response);

        if ($result instanceof Response) {
            return $result;
        }

        $result = $this->events()->triggerUntil(
            __FUNCTION__ . '.dispatch.post', $this, $params, $responseComplete
        );
        if ($result->stopped()) {
            return $result->last();
        }

        return $response;
    }

    public function prepare404()
    {
        $response = $this->response();
        $response->getHeaders()->setStatusCode(404);
        $response->setContent('<h1>Not Found</h1>');
    }
}
