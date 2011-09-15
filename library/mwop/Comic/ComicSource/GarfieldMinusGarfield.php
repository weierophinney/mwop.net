<?php

namespace mwop\Comic\ComicSource;

use mwop\Comic\Comic,
    SimpleXMLElement;

/**
 * @todo merge this, Basic Instructions, and XKCD into generic "RSS" class
 */
class GarfieldMinusGarfield extends AbstractComicSource
{
    protected static $comics = array(
        'g-g' => 'Garfield Minus Garfield',
    );

    protected $comicBase = 'http://garfieldminusgarfield.net';
    protected $feedUrl = 'http://garfieldminusgarfield.net/rss';

    public function fetch()
    {
        // will need to parse feed 
        $sxl = new SimpleXMLElement($this->feedUrl, 0, true);

        // Iterate <item> elements, breaking after first
        $latest = $sxl->channel->item[0];

        // daily is <link> element
        $daily = (string) $latest->link;

        // image is in <description> -- /src="([^"]+)"
        $desc  = (string) $latest->description;
        if (!preg_match('/src="(?P<src>[^"]+)"/', $desc, $matches)) {
            return $this->registerError(sprintf(
                'Garfield Minus Garfield feed does not include image description containing image URL: %s',
                $desc
            ));
        }
        $image = $matches['src'];

        $comic = new Comic(
            /* 'name'  => */ static::$comics['g-g'],
            /* 'link'  => */ $this->comicBase,
            /* 'daily' => */ $daily,
            /* 'image' => */ $image
        );

        return $comic;
    }

    protected function registerError($message)
    {
        $comic = new Comic(
            /* 'name'  => */ static::$comics['g-g'],
            /* 'link'  => */ $this->comicBase
        );
        $comic->setError($message);
        return $comic;
    }
}
