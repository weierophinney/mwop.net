<?php
namespace Mwop;

use InvalidArgumentException;
use RuntimeException;

class Services
{
    protected $factories = array();
    protected $services = array();

    public function add($name, $service)
    {
        $name = strtolower($name);

        if (is_array($service)) {
            $this->services[$name] = $service;
            return $this;
        }

        if (! is_callable($service)
            && (! is_string($service) || ! class_exists($service))
        ) {
            throw new InvalidArgumentException('Invalid service factory provided; must be callable');
        }

        $this->factories[$name] = $service;
        return $this;
    }

    public function get($name)
    {
        $name = strtolower($name);
        if (isset($this->services[$name])) {
            return $this->services[$name];
        }

        if (! isset($this->factories[$name])) {
            throw new RuntimeException(sprintf('No service defined by name "%s"', $name));
        }

        $factory = $this->factories[$name];

        if (is_string($factory)) {
            $instance = new $factory();
        } elseif (is_callable($factory)) {
            $instance = $factory($this);
        }

        $this->services[$name] = $instance;
        return $instance;
    }

    public function has($name)
    {
        $name = strtolower($name);
        return (isset($this->services[$name]) || isset($this->factories[$name]));
    }
}
