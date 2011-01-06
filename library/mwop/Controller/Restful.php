<?php
namespace mwop\Controller;

use mwop\Stdlib\Dispatchable,
    Fig\Request,
    Fig\Response,
    Zend\Http\Request as HttpRequest,
    Zend\Http\Response as HttpResponse,
    Zend\SignalSlot\SignalSlot,
    Zend\SignalSlot\Signals;

abstract class Restful implements Dispatchable
{
    protected $request;
    protected $response;
    protected static $signals;

    abstract public function getList();
    abstract public function get($id);
    abstract public function create($data);
    abstract public function update($id, $data);
    abstract public function delete($id);

    public static function signals(SignalSlot $signals = null)
    {
        if (null !== $signals) {
            static::$signals = $signals;
        } elseif (null === static::$signals) {
            static::$signals = new Signals();
        }
        return static::$signals;
    }

    public static function resetSignals()
    {
        static::$signals = null;
    }

    public function dispatch(Request $request, Response $response = null)
    {
        if (!$request instanceof HttpRequest) {
            throw new \InvalidArgumentException('Expected an HTTP request');
        }

        static::signals()->emit('dispatch.pre', $request, $response);

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

        static::signals()->emit('dispatch.post', $return);
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
