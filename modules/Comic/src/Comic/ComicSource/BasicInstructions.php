<?php

namespace Comic\ComicSource;

use Comic\Comic,
    SimpleXMLElement;

/**
 * @todo merge this, XKCD, and G-G into generic "RSS" class
 */
class BasicInstructions extends AbstractComicSource
{
    protected static $comics = array(
        'basicinstructions' => 'Basic Instructions',
    );

    protected $comicBase = 'http://basicinstructions.net/basic-instructions/';
    protected $feedUrl   = 'http://basicinstructions.net/basic-instructions/rss.xml';

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
                'Basic Instructions feed does not include image description containing image URL: %s',
                $desc
            ));
        }
        $image = $matches['src'];

        $comic = new Comic(
            /* 'name'  => */ static::$comics['basicinstructions'],
            /* 'link'  => */ $this->comicBase,
            /* 'daily' => */ $daily,
            /* 'image' => */ $image
        );

        return $comic;
    }

    protected function registerError($message)
    {
        $comic = new Comic(
            /* 'name'  => */ static::$comics['basicinstructions'],
            /* 'link'  => */ $this->comicBase
        );
        $comic->setError($message);
        return $comic;
    }
}
