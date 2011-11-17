<?php

namespace Comic\ComicSource;

class Xkcd extends AbstractRssSource
{
    protected static $comics = array(
        'xkcd' => 'XKCD',
    );

    protected $comicBase      = 'http://xkcd.com';
    protected $comicShortName = 'xkcd';
    protected $feedUrl        = 'http://xkcd.com/rss.xml';
}
