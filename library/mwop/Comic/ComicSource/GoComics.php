<?php

namespace mwop\Comic\ComicSource;

use mwop\Comic\Comic,
    InvalidArgumentException,
    Zend\Dom\Query as DomQuery;

class GoComics extends AbstractComicSource
{
    protected static $comics = array(
        'pickles'         => 'Pickles',
        'calvinandhobbes' => 'Calvin and Hobbes',
        'fminus'          => 'F Minus',
        'closetohome'     => 'Close to Home',
        'culdesac'        => 'Cul de Sac',
        'nonsequitur'     => 'Non Sequitur',
        'peanuts'         => 'Peanuts',
    );

    protected $comicFormat = 'http://www.gocomics.com/%s';

    protected $dailyFormat = 'http://www.gocomics.com/%s/%s';

    public function __construct($name)
    {
        if (!isset(static::$comics[$name])) {
            throw new InvalidArgumentException(sprintf(
                'The comic "%s" is unsupported by this class',
                $name
            ));
        }
        $this->name = $name;
    }

    public function fetch()
    {
        $url  = sprintf($this->dailyFormat, $this->name, date('Y/m/d'));
        $page = file_get_contents($url);
        if (!$page) {
            return $this->registerError(sprintf(
                'Comic at "%s" is unreachable',
                $url
            ));
        }

        $dom  = new DomQuery($page);
        $r    = $dom->execute('img.strip');
        if (!$r->count()) {
            return false;
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
            /* 'name'  => */ static::$comics[$this->name],
            /* 'link'  => */ sprintf($this->comicFormat, $this->name),
            /* 'daily' => */ $url,
            /* 'image' => */ $imgUrl
        );

        return $comic;
    }

    protected function registerError($message)
    {
        $comic = new Comic(
            /* 'name'  => */ static::$comics[$this->name],
            /* 'link'  => */ sprintf($this->comicFormat, $this->name)
        );
        $comic->setError($message);
        return $comic;
    }
}
