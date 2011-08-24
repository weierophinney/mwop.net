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

    public function fetch()
    {
        $daily = sprintf($this->dailyFormat, date('Y'), date('m'), strtolower(date('l-F-j-Y')));
        $image = sprintf($this->imageFormat, date('ymd'));

        $comic = new Comic(
            /* 'name'  => */ static::$comics['fborfw'],
            /* 'link'  => */ $this->comicBase,
            /* 'daily' => */ $daily,
            /* 'image' => */ $image
        );
    }
}
