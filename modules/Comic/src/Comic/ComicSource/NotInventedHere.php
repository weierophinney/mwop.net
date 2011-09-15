<?php

namespace Comic\ComicSource;

use Comic\Comic,
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
            return $this->registerError(sprintf(
                'Comic at "%s" is unreachable',
                $url
            ));
        }

        $dom  = new DomQuery();
        $dom->setDocumentHtml($page); // force loading as HTML
        $r    = $dom->execute('#comic-content img');
        if (!$r->count()) {
            return $this->registerError(sprintf(
                'Comic at "%s" is unreachable',
                $url
            ));
        }

        $imgUrl = false;
        foreach ($r as $node) {
            if ($node->hasAttribute('src')) {
                $imgUrl = $node->getAttribute('src');
            }
        }

        if (!$imgUrl) {
            return $this->registerError(sprintf(
                'Unable to find image source in "%s"',
                $url
            ));
        }

        $comic = new Comic(
            /* 'name'  => */ static::$comics['nih'],
            /* 'link'  => */ $this->comicBase,
            /* 'daily' => */ $url,
            /* 'image' => */ $imgUrl
        );

        return $comic;
    }

    protected function registerError($message)
    {
        $comic = new Comic(
            /* 'name'  => */ static::$comics['nih'],
            /* 'link'  => */ $this->comicBase
        );
        $comic->setError($message);
        return $comic;
    }
}
