<?php

namespace Comic\ComicSource;

class PennyArcade extends AbstractDomSource
{
    protected static $comics = array(
        'pennyarcade' => 'Penny Arcade',
    );

    protected $comicBase      = 'http://penny-arcade.com/comic';
    protected $comicShortName = 'pennyarcade';
    protected $dailyFormat    = 'http://penny-arcade.com/comic/%s';
    protected $dateFormat     = 'Y/m/d';
    protected $domQuery       = 'div.post.comic img';
}
