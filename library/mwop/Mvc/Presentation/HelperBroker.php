<?php
namespace mwop\Mvc\Presentation;

use Zend\Loader\PluginSpecBroker;

class HelperBroker extends PluginSpecBroker
{
    protected $defaultClassLoader = 'mwop\Mvc\Presentation\HelperLoader';
}
