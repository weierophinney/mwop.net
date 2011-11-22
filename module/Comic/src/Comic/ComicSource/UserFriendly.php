<?php

namespace Comic\ComicSource;

class UserFriendly extends AbstractDomSource
{
    protected static $comics = array(
        'uf' => 'User Friendly',
    );

    protected $comicBase      = 'http://www.userfriendly.org';
    protected $comicShortName = 'uf';
    protected $dailyFormat    = 'http://ars.userfriendly.org/cartoons/?id=%s';
    protected $dateFormat     = 'Ymd';
    protected $domQuery       = 'a img';

    protected function validateImageSrc($src)
    {
        if (strstr($src, 'cartoons/archives/')) {
            return true;
        }
        return false;
    }
}
