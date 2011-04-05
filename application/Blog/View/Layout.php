<?php
namespace Blog\View;

class Layout
{
    public function __construct()
    {
        $requires =<<<EOJ
        dojo.require("dojox.highlight");
        dojo.require("dojox.highlight.languages._all");
        dojo.require("dojox.highlight.languages.pygments.css");
        dojo.addOnLoad(function() {
            dojo.query("div.example pre code").forEach(dojox.highlight.init);
        });
EOJ;

        $this->js = array(
            'source' => array(
                array('code' => $requires),
            )
        );
        $this->css = array(
            array('url' => 'http://fonts.googleapis.com/css?family=Cardo'),
            array('url' => 'http://fonts.googleapis.com/css?family=Lato'),
            array('url' => 'http://fonts.googleapis.com/css?family=Droid+Sans+Mono'),
            array('url' => 'http://ajax.googleapis.com/ajax/libs/dojo/1.6/dojox/highlight/resources/pygments/autumn.css'),
            array('url' => 'http://ajax.googleapis.com/ajax/libs/dojo/1.6/dojox/highlight/resources/highlight.css'),
        );
    }
}
