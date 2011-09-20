<?php
namespace Application\Controller;

use Zend\EventManager\EventCollection,
    Zend\EventManager\EventManager,
    Zend\Stdlib\Dispatchable,
    Zend\Stdlib\RequestDescription as Request,
    Zend\Stdlib\ResponseDescription as Response,
    Zf2Mvc\MvcEvent;

class PageController implements Dispatchable
{
    protected $events;

    public function events(EventCollection $events = null)
    {
        if (null !== $events) {
            $this->events = $events;
        } elseif (null === $this->events) {
            $this->events = new EventManager(array(
                'Zend\Stdlib\Dispatchable', 
                __CLASS__, 
                get_called_class(),
            ));
        }
        return $this->events;
    }

    public function dispatch(Request $request, Response $response = null, $event = null)
    {
        if (!$event) {
            $event = new MvcEvent();
        }
        $event->setRequest($request)
              ->setResponse($response)
              ->setTarget($this);
        $result = $this->events()->triggerUntil('dispatch.pre', $event, function($result) {
            return ($result instanceof Response);
        });
        if ($result->stopped()) {
            return $result->last();
        }

        $routeMatch = $event->getRouteMatch();
        if ($routeMatch) {
            $page = $routeMatch->getParam('page', 404);
        } else {
            $page = 'index';
        }

        $event->setResult($page);
        $result = $this->events()->triggerUntil('dispatch.post', $event, function($result) {
            return ($result instanceof Response);
        });
        if ($result->stopped()) {
            return $result->last();
        }

        return $page;
    }
}
