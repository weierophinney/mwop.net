<?php

namespace Comic\ComicSource;

class ReptilisRex extends AbstractRssSource
{
    protected static $comics = array(
        'reptilis-rex' => 'Reptilis Rex',
    );

    protected $comicBase      = 'http://www.reptilisrex.com';
    protected $comicShortName = 'reptilis-rex';
    protected $feedUrl        = 'http://www.reptilisrex.com/feed/';
}
