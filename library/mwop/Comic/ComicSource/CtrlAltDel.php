<?php

namespace mwop\Comic\ComicSource;

use mwop\Comic\Comic,
    Zend\Dom\Query as DomQuery;

class CtrlAltDel extends AbstractComicSource
{
    protected static $comics = array(
        'ctrlaltdel' => 'Ctrl+Alt+Del',
    );

    protected $comicBase = 'http://www.cad-comic.com';
    protected $dailyFormat = 'http://www.cad-comic.com/cad/%s/';

    public function fetch()
    {
        $url = sprintf($this->dailyFormat, date('Ymd'));
        $page = file_get_contents($url);
        if (!$page) {
            $this->registerError(sprintf(
                'Comic at "%s" is unreachable',
                $url
            ));
            return false;
        }

        $dom  = new DomQuery($page);
        $r    = $dom->execute('#content img');
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
                $src = $node->getAttribute('src');
                if (strstr($src, 'cad-comic.com/comics/cad-')) {
                    $imgUrl = $src;
                }
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
            /* 'name'  => */ static::$comics['ctrlaltdel'],
            /* 'link'  => */ $this->comicBase,
            /* 'daily' => */ $url,
            /* 'image' => */ $imgUrl
        );

        return $comic;
    }
}
