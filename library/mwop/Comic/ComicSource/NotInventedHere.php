<?php

namespace mwop\Comic\ComicSource;

use mwop\Comic\Comic,
    Zend\Dom\Query as DomQuery;

class NotInventedHere extends AbstractComicSource
{
    protected static $comics = array(
        'nih' => 'Not Invented Here',
    );

    protected $comicBase = 'http://notinventedhe.re';
    protected $dailyFormat = 'http://notinventedhe.re/on/%s';

    public function fetch()
    {
        $url = sprintf($this->dailyFormat, date('Y-n-j'));
        $page = file_get_contents($url);
        if (!$page) {
            $this->registerError(sprintf(
                'Comic at "%s" is unreachable',
                $url
            ));
            return false;
        }

        $dom  = new DomQuery();
        $dom->setDocumentHtml($page); // force loading as HTML
        $r    = $dom->execute('#comic-content img');
        if (!$r->count()) {
            $this->registerError(sprintf(
                'Comic at "%s" is unreachable',
                $url
            ));
            return false;
        }

        $imgUrl = false;
        foreach ($r as $node) {
            if ($node->hasAttribute('src')) {
                $imgUrl = $node->getAttribute('src');
            }
        }

        if (!$imgUrl) {
            $this->registerError(sprintf(
                'Unable to find image source in "%s"',
                $url
            ));
            return false;
        }

        $comic = new Comic(
            /* 'name'  => */ static::$comics['nih'],
            /* 'link'  => */ $this->comicBase,
            /* 'daily' => */ $url,
            /* 'image' => */ $imgUrl
        );

        return $comic;
    }
}
