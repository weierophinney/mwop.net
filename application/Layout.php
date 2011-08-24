<?php
use mwop\Mvc\Presentation,
    mwop\Stdlib\UniqueFilteringIterator,
    Zend\Loader\PluginClassLocater;

class Layout extends Presentation
{
    public $javaScripts;
    public $javaScriptCode;
    public $cssLinks;
    public $disqusKey;
    public $titleSegments;

    public function __construct()
    {
        $this->titleSegments  = new UniqueFilteringIterator();
        $this->javaScripts    = new UniqueFilteringIterator();
        $this->javaScriptCode = new UniqueFilteringIterator();
        $this->cssLinks       = new UniqueFilteringIterator();
    }

    public function setDisqusKey($key)
    {
        $this->disqusKey = $key;
    }

    public function title()
    {
        $this->titleSegments->push('phly, boy, phly');
        $segments = $this->titleSegments->toArray();
        return implode(' :: ', $segments);
    }

    public function css()
    {
        $urlHelper = $this->helper('url');
        $this->cssLinks->unshift($urlHelper->generate('/css/site.css'));
        $this->cssLinks->unshift($urlHelper->generate('/css/960.css'));
        $this->cssLinks->unshift($urlHelper->generate('/css/text.css'));
        $this->cssLinks->unshift($urlHelper->generate('/css/reset.css'));

        return $this->cssLinks;
    }

    public function js_src()
    {
        $this->javaScripts->unshift(array(
            'url' => 'http://ajax.googleapis.com/ajax/libs/dojo/1.6/dojo/dojo.xd.js',
            'attributes' => array(
                array('key' => 'djConfig', 'value' => 'isDebug:true, parseOnLoad:true'),
            ),
        ));
        return $this->javaScripts;
    }

    public function js_code()
    {
        if (0 == count($this->javaScriptCode)) {
            return false;
        }
        return array('code' => $this->javaScriptCode);
    }

    public function home_url()
    {
        return $this->helper('url')->generate('/');
    }

    public function blog_url()
    {
        return $this->helper('url')->generate(array(), array('name' => 'blog'));
    }

    public function comics_url()
    {
        return $this->helper('url')->generate(array(), array('name' => 'comics'));
    }

    public function resume_url()
    {
        return $this->helper('url')->generate(array(), array('name' => 'resume'));
    }
}
