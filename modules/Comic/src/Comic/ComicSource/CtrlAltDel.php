<?php

namespace Comic\ComicSource;

use Comic\Comic,
    Zend\Dom\Query as DomQuery;

class CtrlAltDel extends AbstractComicSource
{
    protected static $comics = array(
        'ctrlaltdel' => 'Ctrl+Alt+Del',
    );

    protected $comicBase = 'http://www.cad-comic.com/cad/';
    protected $dailyFormat = 'http://www.cad-comic.com/cad/%s/';

    public function fetch()
    {
        $page = file_get_contents($this->comicBase);
        if (!$page) {
            return $this->registerError(sprintf(
                'Comic at "%s" is unreachable',
                $this->comicBase
            ));
        }

        $dom  = new DomQuery($page);
        $r    = $dom->execute('#content img');
        if (!$r->count()) {
            return $this->registerError(sprintf(
                'Comic at "%s" is unreachable',
                $url
            ));
        }

        $imgUrl = false;
        foreach ($r as $node) {
            if ($node->hasAttribute('src')) {
                $src = $node->getAttribute('src');
                if (strstr($src, 'cad-comic.com/comics/cad-')) {
                    $imgUrl = $src;
                    break;
                }
            }
        }

        if (!$imgUrl) {
            return $this->registerError(sprintf(
                'Unable to find image source in "%s"',
                $url
            ));
        }

        $daily  = sprintf($this->dailyFormat, date('Ymd'));
        if (preg_match('#cad-(?P<date>\d{8})-[a-z0-9]+\.png#', $imgUrl, $matches)) {
            $daily = sprintf($this->dailyFormat, $matches['date']);
        }

        $comic = new Comic(
            /* 'name'  => */ static::$comics['ctrlaltdel'],
            /* 'link'  => */ $this->comicBase,
            /* 'daily' => */ $daily,
            /* 'image' => */ $imgUrl
        );

        return $comic;
    }

    protected function registerError($message)
    {
        $comic = new Comic(
            /* 'name'  => */ static::$comics['ctrlaltdel'],
            /* 'link'  => */ $this->comicBase
        );
        $comic->setError($message);
        return $comic;
    }
}
