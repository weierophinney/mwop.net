<?php
namespace mwop\Mvc\Presentation;

use Zend\Loader\PluginClassLoader;

class HelperLoader extends PluginClassLoader
{
    protected $plugins = array(
        'url' => 'mwop\Mvc\Presentation\Url',
    );
}
