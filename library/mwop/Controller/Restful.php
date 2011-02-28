<?php
namespace mwop\Controller;

use mwop\Stdlib\Dispatchable,
    mwop\Stdlib\Entity,
    mwop\Stdlib\Resource,
    Fig\Request,
    Fig\Response,
    Zend\Http\Request as HttpRequest,
    Zend\Http\Response as HttpResponse,
    Zend\EventManager\EventCollection,
    Zend\EventManager\EventManager,
    Zend\Filter\InputFilter;

abstract class Restful implements Dispatchable
{
    protected $request;
    protected $response;
    protected $events;
    protected $resource;

    abstract public function resource(Resource $resource = null);

    public function getList()
    {
        $entities = $this->resource()->getAll()->toArray();
        return array( 
            'entities' => array_values($entities),
        );
    }

    public function get($id)
    {
        return array(
            'entity' => $this->resource()->get($id)->toArray(),
        );
    }

    public function create($data)
    {
        $entity = $this->resource()->create($data);
        if ($entity instanceof Entity) {
            return array(
                'entity'  => $entity->toArray(),
                'success' => true,
            );
        }
        return array(
            'success' => false,
            'errors'  => $entity->getMessages(),
        );
    }

    public function update($id, $data)
    {
        return array(
            'entity' => $this->resource()->update($id, $data)->toArray(),
        );
    }

    public function delete($id)
    {
        return array(
            'success' => $this->resource()->delete($id),
        );
    }

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

        if ($action = $request->getMetadata('action', false)) {
            // Handle arbitrary methods, ending in Action
            $method = $this->getMethodFromAction($action);
            if (!method_exists($this, $method)) {
                $this->prepare404();
                return $response;
            }
            $return = $this->$method();
        } else {
            // RESTful methods
            switch (strtolower($request->getMethod())) {
                case 'get':
                    if (null !== $id = $request->getMetadata('id')) {
                        $return = $this->get($id);
                        break;
                    }
                    $return = $this->getList();
                    break;
                case 'post':
                    $return = $this->create($request->post()->toArray());
                    break;
                case 'put':
                    if (null === $id = $request->getMetadata('id')) {
                        throw new \DomainException('Missing identifier');
                    }
                    $params = $request->getContent();
                    $params = parse_str($params);
                    $return = $this->update($id, $params);
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

    protected function getMethodFromAction($action)
    {
        $method = str_replace(array('-', '.', '_'), ' ', $action);
        $method = ucwords($method);
        return str_replace(' ', '', $method) . 'Action';
    }
}
