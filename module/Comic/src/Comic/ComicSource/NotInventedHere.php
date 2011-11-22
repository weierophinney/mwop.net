<?php

namespace Comic\ComicSource;

class NotInventedHere extends AbstractDomSource
{
    protected static $comics = array(
        'nih' => 'Not Invented Here',
    );

    protected $comicBase      = 'http://notinventedhe.re';
    protected $comicShortName = 'nih';
    protected $dailyFormat    = 'http://notinventedhe.re/on/%s';
    protected $dateFormat     = 'Y-n-j';
    protected $domQuery       = '#comic-content img';
    protected $domIsHtml      = true;
}
