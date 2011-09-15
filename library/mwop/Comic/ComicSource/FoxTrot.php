<?php

namespace mwop\Comic\ComicSource;

use mwop\Comic\Comic,
    DateTime,
    Zend\Dom\Query as DomQuery;

class FoxTrot extends AbstractComicSource
{
    protected static $comics = array(
        'foxtrot' => 'FoxTrot',
    );

    protected $comicFormat = 'http://www.foxtrot.com';

    protected $dailyFormat = 'http://www.foxtrot.com/%s';

    public function fetch()
    {
        $page = file_get_contents($this->comicFormat);
        if (!$page) {
            return $this->registerError(sprintf(
                'Comic at "%s" is unreachable',
                $url
            ));
        }

        $dom  = new DomQuery($page);
        $r    = $dom->execute('#comic img');
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
                break;
            }
        }

        if (!$imgUrl) {
            return $this->registerError(sprintf(
                'Unable to find image source in "%s"',
                $url
            ));
        }

        $daily = $this->getDailyUrl();

        $comic = new Comic(
            /* 'name'  => */ static::$comics['foxtrot'],
            /* 'link'  => */ $this->comicFormat,
            /* 'daily' => */ $daily,
            /* 'image' => */ $imgUrl
        );

        return $comic;
    }

    protected function getDailyUrl()
    {
        $date      = new DateTime('now');
        $dayOfWeek = $date->format('l');
        switch ($dayOfWeek) {
            case 'Sunday':
                break;
            default:
                $date = new DateTime('last Sunday');
                break;
        }

        $url = sprintf($this->dailyFormat, $date->format('Y/m/d/'));
        return $url;
    }

    protected function registerError($message)
    {
        $comic = new Comic(
            /* 'name'  => */ static::$comics['foxtrot'],
            /* 'link'  => */ $this->comicFormat
        );
        $comic->setError($message);
        return $comic;
    }
}
