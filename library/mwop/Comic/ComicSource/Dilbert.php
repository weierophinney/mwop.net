<?php

namespace mwop\Comic\ComicSource;

use mwop\Comic\Comic,
    Zend\Dom\Query as DomQuery;

class Dilbert extends AbstractComicSource
{
    protected static $comics = array(
        'dilbert' => 'Dilbert',
    );

    protected $comicFormat = 'http://dilbert.com';

    protected $dailyFormat = 'http://dilbert.com/strips/comic/%s/';

    public function fetch()
    {
        $url  = sprintf($this->dailyFormat, date('Y-m-d'));
        $page = file_get_contents($url);
        if (!$page) {
            return $this->registerError(sprintf(
                'Comic at "%s" is unreachable',
                $url
            ));
        }

        $dom  = new DomQuery($page);
        $r    = $dom->execute('div.STR_Image img');
        if (!$r->count()) {
            return $this->registerError(sprintf(
                'Comic at "%s" is unreachable',
                $url
            ));
        }

        $imgUrl = false;
        foreach ($r as $node) {
            if ($node->hasAttribute('src')) {
                $imgUrl = $this->comicFormat . $node->getAttribute('src');
            }
        }

        if (!$imgUrl) {
            return $this->registerError(sprintf(
                'Unable to find image source in "%s"',
                $url
            ));
        }

        $comic = new Comic(
            /* 'name'  => */ static::$comics['dilbert'],
            /* 'link'  => */ $this->comicFormat,
            /* 'daily' => */ $url,
            /* 'image' => */ $imgUrl
        );

        return $comic;
    }

    protected function registerError($message)
    {
        $comic = new Comic(
            /* 'name'  => */ static::$comics['dilbert'],
            /* 'link'  => */ $this->comicFormat
        );
        $comic->setError($message);
        return $comic;
    }
}
