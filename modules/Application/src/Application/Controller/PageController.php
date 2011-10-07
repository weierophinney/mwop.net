<?php
namespace Application\Controller;

use Zend\EventManager\EventDescription as Event,
    Zend\EventManager\EventCollection,
    Zend\EventManager\EventManager,
    Zend\Stdlib\Dispatchable,
    Zend\Stdlib\RequestDescription as Request,
    Zend\Stdlib\ResponseDescription as Response,
    Zend\Mvc\InjectApplicationEvent,
    Zend\Mvc\MvcEvent;

class PageController implements Dispatchable, InjectApplicationEvent
{
    protected $event;
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

    public function setEvent(Event $event)
    {
        if ($event instanceof MvcEvent) {
            $this->event = $event;
            return;
        }
        $params = $event->getParams();
        $this->event = new MvcEvent();
        $this->event->setParams($params);
    }

    public function getEvent()
    {
        if (!$this->event) {
            $this->setEvent(new MvcEvent);
        }
        return $this->event;
    }

    public function dispatch(Request $request, Response $response = null)
    {
        $event = $this->getEvent();
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
            $page = $routeMatch->getParam('action', '404');
        } else {
            $page = 'index';
        }

        $event->setResult($page);
        return $page;
    }
}
