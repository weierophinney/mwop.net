<?php

namespace Comic\ComicSource;

use DateTime;

class FoxTrot extends AbstractDomSource
{
    protected static $comics = array(
        'foxtrot' => 'FoxTrot',
    );

    protected $comicBase      = 'http://www.foxtrot.com';
    protected $comicShortName = 'foxtrot';
    protected $dailyFormat    = 'http://www.foxtrot.com/%s';
    protected $dateFormat     = 'Y/m/d/';
    protected $domQuery       = '#comic img';
    protected $useComicBase   = true;

    protected function getDailyUrl($imgUrl)
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

        $url = sprintf($this->dailyFormat, $date->format($this->dateFormat));
        return $url;
    }
}
