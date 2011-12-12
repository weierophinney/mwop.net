<?php

namespace Cache;

use Zend\Cache\Frontend as CacheFrontend,
    Zend\EventManager\EventCollection as Events,
    Zend\EventManager\EventDescription as Event,
    Zend\EventManager\ListenerAggregate,
    Zend\Http\Request,
    Zend\Mvc\MvcEvent;

class Listener implements ListenerAggregate
{
    protected $cache;
    protected $listeners = array();
    protected $rules = array();
    protected $skipCacheDueTo = false;

    public function __construct(CacheFrontend $cache, $rules = array())
    {
        $this->cache = $cache;

        $this->addRule(function($e) {
            if (!$e instanceof MvcEvent) {
                return;
            }
            $request = $e->getRequest();

            if ($request->getMethod() != 'GET') {
                // Only cache GET requests
                return true;
            }

            // Rule does not match; okay to cache
            return false;
        });

        foreach ($rules as $rule) {
            $this->addRule($rule);
        }
    }

    public function addRule($rule)
    {
        if (!is_callable($rule)) {
            throw new InvalidArgumentException(sprintf(
                '%s expects a callable; received "%s"',
                __METHOD__,
                (is_object($rule) ? get_class($rule) : gettype($rule))
            ));
        }
        $this->rules[] = $rule;
    }

    public function attach(Events $e)
    {
        $this->listeners[] = $e->attach('dispatch', array($this, 'queryCache'), 99);
        $this->listeners[] = $e->attach('dispatch', array($this, 'saveToCache'), -10000);
    }

    public function detach(Events $e)
    {
        foreach ($this->listeners as $listener) {
            $e->detach($listener);
        }
    }

    public function queryCache(Event $e)
    {
        if ($this->omit($e)) {
            return;
        }

        $request = $e->getRequest();
        $key     = $this->createKey($request);
        if (false === ($found = $this->cache->load($key))) {
            return;
        }

        $status   = $found['status'];
        $headers  = $found['headers'];
        $content  = $found['content'];
        $response = $e->getReponse();
        $response->headers()->addHeaders($headers);
        $response->setStatusCode($status);
        $response->setContent($content);
        return $response;
    }

    public function saveToCache(Event $e)
    {
        if ($this->omit($e)) {
            return;
        }

        $key     = $this->createKey($e->getRequest());
        $reponse = $e->getResponse();
        $status  = $response->getStatusCode();
        $headers = $response->headers()->toArray();
        $content = $response->getContent($content);
        $data    = compact('status', 'headers', 'content');

        $this->cache->save($data, $key);
    }

    public function getReasonSkipped()
    {
        return $this->skipCacheDueTo;
    }

    protected function createKey(Request $request)
    {
        $uri = $request->uri()->toString();
        $key = md5($uri);
        return $key;
    }

    protected function omit(Event $e)
    {
        foreach ($this->rules as $rule) {
            if (call_user_func($rule, $e)) {
                $this->skipCacheDueTo = $rule;
                return true;
            }
        }
        return false;
    }
}
