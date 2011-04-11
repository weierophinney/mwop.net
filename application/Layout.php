<?php
use mwop\Mvc\Presentation,
    mwop\Stdlib\UniqueFilteringIterator,
    Zend\Loader\PluginClassLocater;

class Layout extends Presentation
{
    public $javaScripts;
    public $javaScriptCode;
    public $cssLinks;

    public function __construct()
    {
        $this->javaScripts    = new UniqueFilteringIterator();
        $this->javaScriptCode = new UniqueFilteringIterator();
        $this->cssLinks       = new UniqueFilteringIterator();
    }
}
