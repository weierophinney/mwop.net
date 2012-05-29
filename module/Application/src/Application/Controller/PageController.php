<?php
namespace Application\Controller;

use Zend\EventManager\EventInterface as Event,
    Zend\EventManager\EventManagerAwareInterface,
    Zend\EventManager\EventManagerInterface,
    Zend\EventManager\EventManager,
    Zend\EventManager\EventsCapableInterface,
    Zend\Stdlib\DispatchableInterface,
    Zend\Stdlib\RequestInterface as Request,
    Zend\Stdlib\ResponseInterface as Response,
    Zend\Mvc\InjectApplicationEventInterface,
    Zend\Mvc\MvcEvent,
    Zend\View\Model\ViewModel;

class PageController implements 
    DispatchableInterface, 
    EventManagerAwareInterface, 
    EventsCapableInterface,
    InjectApplicationEventInterface
{
    protected $event;
    protected $events;

    public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers(array(
                'Zend\Stdlib\DispatchableInterface', 
                __CLASS__, 
                get_class($this),
        ));
        $events->attach('dispatch', array($this, 'execute'));
        $this->events = $events;
    }

    public function events()
    {
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

        $model = new ViewModel();
        $model->setTemplate('pages/' . $page);
        $model->setCaptureTo('content');
        $event->setResult($model);
        return $model;
    }
}
