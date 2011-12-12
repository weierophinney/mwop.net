<?php

namespace Cache;

use Zend\EventManager\EventCollection as Events,
    Zend\EventManager\ListenerAggregate;

class Listener implmenets ListenerAggregate
{
    protected $listeners = array();

    public function attach(Events $e)
    {
    }

    public function detach(Events $e)
    {
        foreach ($this->listeners as $listener) {
            $e->detach($listener);
        }
    }
}
