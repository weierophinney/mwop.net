<?php

namespace mwop\Comic\ComicSource;

use mwop\Comic\Comic,
    DOMDocument,
    DOMXPath,
    SimpleXMLElement;

class ScenesFromAMultiverse extends AbstractComicSource
{
    protected static $comics = array(
        'sfam' => 'Scenes From A Multiverse',
    );

    protected $comicBase = 'http://amultiverse.com';
    protected $feedUrl   = 'http://feeds.feedburner.com/ScenesFromAMultiverse';

    public function fetch()
    {
        // will need to parse feed at http://xkcd.com/rss.xml
        $sxl = new SimpleXMLElement($this->feedUrl, LIBXML_NOCDATA, true);

        // Iterate <item> elements, breaking after first
        $latest = $sxl->channel->item[0];

        // daily is <guid> element
        $daily = (string) $latest->guid;

        // Parse description
        $desc   = (string) $latest->description;
        $dom    = new DOMDocument();
        $dom->loadHTML($desc);
        $xpath  = new DOMXPath($dom);
        $result = $xpath->query('//a/img');
        if (!$result || !$result->length) {
            return $this->registerError(sprintf(
                'Unable to find Scenes From A Multiverse comic image in description ("%s")',
                $desc
            ));
        }
        $img = $result->item(0);

        if (!$img->hasAttribute('src')) {
            return $this->registerError(sprintf(
                'Scenes From A Multiverse image does not contain a src attribute: %s',
                $desc
            ));
        }
        $image = $img->getAttribute('src');

        $comic = new Comic(
            /* 'name'  => */ static::$comics['sfam'],
            /* 'link'  => */ $this->comicBase,
            /* 'daily' => */ $daily,
            /* 'image' => */ $image
        );

        return $comic;
    }

    protected function registerError($message)
    {
        $comic = new Comic(
            /* 'name'  => */ static::$comics['sfam'],
            /* 'link'  => */ $this->comicBase
        );
        $comic->setError($message);
        return $comic;
    }
}
