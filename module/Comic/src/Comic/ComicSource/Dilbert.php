<?php

namespace Comic\ComicSource;

class Dilbert extends AbstractDomSource
{
    protected static $comics = array(
        'dilbert' => 'Dilbert',
    );

    protected $comicBase      = 'http://dilbert.com';
    protected $comicShortName = 'dilbert';
    protected $dailyFormat    = 'http://dilbert.com/strips/comic/%s/';
    protected $dateFormat     = 'Y-m-d';
    protected $domQuery       = 'div.STR_Image img';

    protected function formatImageSrc($src)
    {
        return $this->comicBase . $src;
    }
}
