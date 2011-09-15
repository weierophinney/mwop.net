<?php
namespace Application\Controller;

use Zend\EventManager\EventCollection,
    Zend\EventManager\EventManager,
    Zend\Stdlib\Dispatchable,
    Zend\Stdlib\RequestDescription as Request,
    Zend\Stdlib\ResponseDescription as Response;

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

    public function dispatch(Request $request, Response $response = null)
    {
        $params = compact('request', 'response');
        $result = $this->events()->triggerUntil('dispatch.pre', $this, $params, function($result) {
            return ($result instanceof Response);
        });
        if ($result->stopped()) {
            return $result->last();
        }

        $routeMatch = $request->getMetadata('route-match', false);
        if ($routeMatch) {
            $page = $routeMatch->getParam('page', 404);
        } else {
            $page = 'index';
        }

        $params['__RESULT__'] = $page;
        $result = $this->events()->triggerUntil('dispatch.post', $this, $params, function($result) {
            return ($result instanceof Response);
        });
        if ($result->stopped()) {
            return $result->last();
        }

        return $page;
    }
}
