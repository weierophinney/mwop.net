<?php
namespace mwop\Mvc\Presentation;

use Zend\Loader\PluginBroker;

class HelperBroker extends PluginBroker
{
    protected $defaultClassLoader = 'mwop\Mvc\Presentation\HelperLoader';

    public function __construct($options = null)
    {
        parent::__construct($options);
    }
}
