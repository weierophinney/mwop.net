<?php
namespace mwop\Controller;

use mwop\Stdlib\Dispatchable,
    Fig\Request,
    Fig\Response,
    Zend\Http\Request as HttpRequest,
    Zend\Http\Response as HttpResponse,
    Zend\EventManager\EventCollection,
    Zend\EventManager\EventManager;

abstract class Restful implements Dispatchable
{
    protected $request;
    protected $response;
    protected $events;

    abstract public function getList();
    abstract public function get($id);
    abstract public function create($data);
    abstract public function update($id, $data);
    abstract public function delete($id);

    public function events(EventCollection $events = null)
    {
        if (null !== $events) {
            $this->events = $events;
        } elseif (null === $this->events) {
            $this->events = new EventManager(array(__CLASS__, get_called_class()));
        }
        return $this->events;
    }

    public function dispatch(Request $request, Response $response = null)
    {
        if (!$request instanceof HttpRequest) {
            throw new \InvalidArgumentException('Expected an HTTP request');
        }

        // Emit pre-dispatch signal, passing:
        // - request, response
        // If a handler returns a response object, return it immediately
        $params = compact('request', 'response');
        $result = $this->events()->triggerUntil(__FUNCTION__ . '.pre', $this, $params, function($result) {
            return ($result instanceof Response);
        });
        if ($result->stopped()) {
            return $result->last();
        }

        $this->setRequest($request)
             ->setResponse($response);

        switch (strtolower($request->getMethod())) {
            case 'get':
                if (null !== $id = $request->getMetadata('id')) {
                    $return = $this->get($id);
                    break;
                }
                $return = $this->getAll();
                break;
            case 'post':
                $return = $this->post($request->post());
                break;
            case 'put':
                if (null === $id = $request->getMetadata('id')) {
                    throw new \DomainException('Missing identifier');
                }
                $params = $request->getContent();
                $return = $this->put($id, $params);
                break;
            case 'delete':
                if (null === $id = $request->getMetadata('id')) {
                    throw new \DomainException('Missing identifier');
                }
                $return = $this->delete($id);
                break;
            default:
                throw new \DomainException('Invalid HTTP method!');
        }

        // Emit post-dispatch signal, passing:
        // - return from method, request, response
        // If a handler returns a response object, return it immediately
        $params['__RESULT__'] = $return;
        $result = $this->events()->triggerUntil(__FUNCTION__ . '.post', $this, $params, function($result) {
            return ($result instanceof Response);
        });
        if ($result->stopped()) {
            return $result->last();
        }

        return $return;
    }

    /**
     * Set request object
     *
     * @param  Request $request
     * @return Restful
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }
    
    /**
     * Get request object
     *
     * @return Request
     */
    public function getRequest()
    {
        if (null === $this->request) {
            $this->setRequest(new HttpRequest());
        }
        return $this->request;
    }

    /**
     * Set response object
     *
     * @param  null|Response $response
     * @return $this
     */
    public function setResponse(Response $response = null)
    {
        if (null === $response) {
            return $this;
        }
        $this->response = $response;
        return $this;
    }
    
    /**
     * Get response object
     *
     * @return Response
     */
    public function getResponse()
    {
        if (null === $this->response) {
            $this->setResponse(new HttpResponse());
        }
        return $this->response;
    }
}
