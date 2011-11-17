<?php

namespace Comic\ComicSource;

use InvalidArgumentException;

class GoComics extends AbstractDomSource
{
    protected static $comics = array(
        'pickles'         => 'Pickles',
        'calvinandhobbes' => 'Calvin and Hobbes',
        'fminus'          => 'F Minus',
        'closetohome'     => 'Close to Home',
        'culdesac'        => 'Cul de Sac',
        'nonsequitur'     => 'Non Sequitur',
        'peanuts'         => 'Peanuts',
    );

    protected $comicFormat = 'http://www.gocomics.com/%s';
    protected $dateFormat  = 'Y/m/d';
    protected $domQuery    = 'img.strip';

    public function __construct($name)
    {
        if (!isset(static::$comics[$name])) {
            throw new InvalidArgumentException(sprintf(
                'The comic "%s" is unsupported by this class',
                $name
            ));
        }
        $this->comicShortName = $name;
        $this->comicBase      = sprintf($this->comicFormat, $name);
        $this->dailyFormat    = $this->comicBase . '/%s';
    }
}
