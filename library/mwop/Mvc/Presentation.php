<?php
namespace mwop\Mvc;

use mwop\Stdlib\ViewPresentation,
    Fig\Request,
    Zend\Loader\Broker,
    Zend\Loader\PluginSpecBroker;

class Presentation implements ViewPresentation
{
    protected $broker;
    protected $layout = 'layout';

    public function layout($view = null)
    {
        if (null !== $view) {
            $this->layout = $view;
        }
        return $this->layout;
    }

    public function helper($spec = null)
    {
        if (null !== $spec) {
            if ($spec instanceof Broker) {
                // Register new broker instance
                $this->broker = $spec;
                return $spec;
            }
            if (is_object($spec)) {
                // Register explicit helper object with broker
                if (1 < func_num_args()) {
                    $name = func_get_arg(1);
                } else {
                    $name = get_class($spec);
                    if (strstr($name, '\\')) {
                        $name = substr($name, strrpos($name, '\\'));
                    }
                }
                $this->helper()->register($name, $spec);
                return $this->broker;
            } elseif (is_string($spec)) {
                // Retrieve a helper from the broker
                return $this->helper()->load($spec);
            }
        } elseif (null === $this->broker) {
            // lazy-load the broker
            $this->broker = new Presentation\HelperBroker();
        }
        // Return the broker
        return $this->broker;
    }
}
