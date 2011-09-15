<?php

namespace mwop\Comic\ComicSource;

use mwop\Comic\Comic,
    Zend\Dom\Query as DomQuery;

class UserFriendly extends AbstractComicSource
{
    protected static $comics = array(
        'uf' => 'User Friendly',
    );

    protected $comicBase = 'http://www.userfriendly.org';

    protected $dailyFormat = 'http://ars.userfriendly.org/cartoons/?id=%s';

    public function fetch()
    {
        $url  = sprintf($this->dailyFormat, date('Ymd'));
        $page = file_get_contents($url);
        if (!$page) {
            return $this->registerError(sprintf(
                'Comic at "%s" is unreachable',
                $url
            ));
        }

        $dom  = new DomQuery($page);
        $r    = $dom->execute('a img');
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
                if (strstr($src, 'cartoons/archives/')) {
                    $imgUrl = $src;
                }
            }
        }

        if (!$imgUrl) {
            return $this->registerError(sprintf(
                'Unable to find image source in "%s"',
                $url
            ));
        }

        $comic = new Comic(
            /* 'name'  => */ static::$comics['uf'],
            /* 'link'  => */ $this->comicBase,
            /* 'daily' => */ $url,
            /* 'image' => */ $imgUrl
        );

        return $comic;
    }

    protected function registerError($message)
    {
        $comic = new Comic(
            /* 'name'  => */ static::$comics['uf'],
            /* 'link'  => */ $this->comicBase
        );
        $comic->setError($message);
        return $comic;
    }
}
