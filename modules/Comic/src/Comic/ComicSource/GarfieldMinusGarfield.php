<?php

namespace Comic\ComicSource;

class GarfieldMinusGarfield extends AbstractRssSource
{
    protected static $comics = array(
        'g-g' => 'Garfield Minus Garfield',
    );

    protected $comicBase      = 'http://garfieldminusgarfield.net';
    protected $comicShortName = 'g-g';
    protected $feedUrl        = 'http://garfieldminusgarfield.net/rss';
}
