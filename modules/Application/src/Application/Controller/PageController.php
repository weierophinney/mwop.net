<?php
namespace Application\Controller;

use Zend\EventManager\EventCollection,
    Zend\EventManager\EventManager,
    Zend\Stdlib\Dispatchable,
    Zend\Stdlib\RequestDescription as Request,
    Zend\Stdlib\ResponseDescription as Response,
    Zend\Mvc\MvcEvent;

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
            $this->events->attach('dispatch', array($this, 'execute'));
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

        $result = $this->events()->trigger('dispatch', $event, function($test) {
            return ($test instanceof Response);
        });
        if ($result->stopped()) {
            return $result->last();
        }

        return $event->getResult();
    }

    public function execute(MvcEvent $event)
    {
        $routeMatch = $event->getRouteMatch();
        if ($routeMatch) {
            $page = $routeMatch->getParam('page', '404');
        } else {
            $page = 'index';
        }

        $event->setResult($page);
        return $page;
    }
}
