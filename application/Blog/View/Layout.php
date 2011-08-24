<?php
namespace Blog\View;

use mwop\Mvc\Presentation,
    mwop\Stdlib\UniqueFilteringIterator;

class Layout
{
    public static function setup(Presentation $layout)
    {
        if (!isset($layout->javaScriptCode)) {
            $layout->javaScriptCode = new UniqueFilteringIterator;
        }
        if (!isset($layout->cssLinks)) {
            $layout->cssLinks = new UniqueFilteringIterator;
        }
        if (!isset($layout->titleSegments)) {
            $layout->titleSegments = new UniqueFilteringIterator;
        }
        $layout->titleSegments->unshift('Blog');


        $requires =<<<EOJ
        dojo.require("dojox.highlight");
        dojo.require("dojox.highlight.languages._all");
        dojo.require("dojox.highlight.languages.pygments.css");
        dojo.addOnLoad(function() {
            dojo.query("div.example pre code").forEach(dojox.highlight.init);
        });
EOJ;
        $layout->javaScriptCode->push($requires);

        $layout->cssLinks->push('http://ajax.googleapis.com/ajax/libs/dojo/1.6/dojox/highlight/resources/pygments/autumn.css');
        $layout->cssLinks->push('http://ajax.googleapis.com/ajax/libs/dojo/1.6/dojox/highlight/resources/highlight.css');
    }
}
