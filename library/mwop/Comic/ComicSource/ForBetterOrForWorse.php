<?php

namespace mwop\Comic\ComicSource;

use mwop\Comic\Comic;

class ForBetterOrForWorse extends AbstractComicSource
{
    protected static $comics = array(
        'fborfw' => 'For Better or For Worse',
    );

    protected $comicBase = 'http://fborfw.com';

    protected $dailyFormat = 'http://fborfw.com/strip_fix/%s/%s/%s.php';
    protected $imageFormat = 'http://fborfw.com/strip_fix/strips/fb_c%s.gif';
    protected $sundayImageFormat = 'http://fborfw.com/strip_fix/strips/%s.jpg';

    public function fetch()
    {
        $daily = sprintf($this->dailyFormat, date('Y'), date('m'), strtolower(date('l-F-j-Y')));

        switch (strtolower(date('l'))) {
            case 'sunday':
                $image = sprintf($this->sundayImageFormat, date('ymd'));
                break;
            default:
                $image = sprintf($this->imageFormat, date('ymd'));
                break;
        }

        $comic = new Comic(
            /* 'name'  => */ static::$comics['fborfw'],
            /* 'link'  => */ $this->comicBase,
            /* 'daily' => */ $daily,
            /* 'image' => */ $image
        );
        return $comic;
    }
}
