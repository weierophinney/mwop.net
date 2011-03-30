<?php
namespace Site\Controller;

use mwop\Stdlib\Dispatchable,
    Fig\Request,
    Fig\Response,
    Zend\Http\Request as HttpRequest,
    Zend\Http\Response as HttpResponse,
    Zend\EventManager\EventCollection,
    Zend\EventManager\EventManager;

class Page implements Dispatchable
{
    protected $events;

    public function events(EventCollection $events = null)
    {
        if (null !== $events) {
            $this->events = $events;
        } elseif (null === $this->events) {
            $this->events = new EventManager(array(
                'mwop\Stdlib\Dispatchable', __CLASS__, get_called_class()
            ));
        }
        return $this->events;
    }

    public function dispatch(Request $request, Response $response = null)
    {
        $params = compact('request', 'response');
        $result = $this->events()->triggerUntil(__FUNCTION__ . '.pre', $this, $params, function($result) {
            return ($result instanceof Response);
        });
        if ($result->stopped()) {
            return $result->last();
        }

        $page = $request->getMetadata('page', '404');

        $params['__RESULT__'] = $page;
        $result = $this->events()->triggerUntil(__FUNCTION__ . '.post', $this, $params, function($result) {
            return ($result instanceof Response);
        });
        if ($result->stopped()) {
            return $result->last();
        }

        return $page;
    }
}
