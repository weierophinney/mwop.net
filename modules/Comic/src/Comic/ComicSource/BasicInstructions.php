<?php

namespace Comic\ComicSource;

class BasicInstructions extends AbstractRssSource
{
    protected static $comics = array(
        'basicinstructions' => 'Basic Instructions',
    );

    protected $comicBase      = 'http://basicinstructions.net/basic-instructions/';
    protected $comicShortName = 'basicinstructions';
    protected $feedUrl        = 'http://basicinstructions.net/basic-instructions/rss.xml';
}
