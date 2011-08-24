<?php

namespace mwop\Comic\ComicSource;

use mwop\Comic\Comic,
    SimpleXMLElement;

class Xkcd extends AbstractComicSource
{
    protected static $comics = array(
        'xkcd' => 'XKCD',
    );

    protected $comicBase = 'http://xkcd.com';
    protected $feedUrl = 'http://xkcd.com/rss.xml';

    public function fetch()
    {
        // will need to parse feed at http://xkcd.com/rss.xml
        $sxl = new SimpleXMLElement($this->feedUrl, 0, true);

        // Iterate <item> elements, breaking after first
        $latest = $sxl->channel->item[0];

        // daily is <link> element
        $daily = (string) $latest->link;

        // image is in <description> -- /src="([^"]+)"
        $desc  = (string) $latest->description;
        if (!preg_match('/src="(?P<src>[^"]+)"/', $desc, $matches)) {
            $this->registerError(sprintf(
                'XKCD feed does not include image description containing image URL: %s',
                $desc
            ));
            return false;
        }
        $image = $matches['src'];

        $comic = new Comic(
            /* 'name'  => */ static::$comics['xkcd'],
            /* 'link'  => */ $this->comicBase,
            /* 'daily' => */ $daily,
            /* 'image' => */ $image
        );

        return $comic;
    }
}

