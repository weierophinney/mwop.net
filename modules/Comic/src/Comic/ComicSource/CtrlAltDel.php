<?php

namespace Comic\ComicSource;

class CtrlAltDel extends AbstractDomSource
{
    protected static $comics = array(
        'ctrlaltdel' => 'Ctrl+Alt+Del',
    );

    protected $comicBase      = 'http://www.cad-comic.com/cad/';
    protected $comicShortName = 'ctrlaltdel';
    protected $dailyFormat    = 'http://www.cad-comic.com/cad/%s/';
    protected $domQuery       = '#content img';
    protected $useComicBase   = true;

    protected function validateImageSrc($src)
    {
        if (strstr($src, 'cad-comic.com/comics/cad-')) {
            return true;
        }
        return false;
    }

    protected function getDailyUrl($imgUrl)
    {
        $daily  = sprintf($this->dailyFormat, date('Ymd'));
        if (preg_match('#cad-(?P<date>\d{8})-[a-z0-9]+\.png#', $imgUrl, $matches)) {
            $daily = sprintf($this->dailyFormat, $matches['date']);
        }
        return $daily;
    }
}
