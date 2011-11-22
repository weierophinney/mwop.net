<?php

namespace Comic\ComicSource;

use Comic\Comic,
    SimpleXMLElement;

abstract class AbstractRssSource extends AbstractComicSource
{
    /**
     * @var string Base URL to landing page for comic
     */
    protected $comicBase;

    /**
     * @var string Short name of comic
     */
    protected $comicShortName;

    /**
     * @var string URI to a feed
     */
    protected $feedUrl;

    public function fetch()
    {
        // Retrieve feed to parse
        $sxl = new SimpleXMLElement($this->feedUrl, 0, true);

        // Iterate <item> elements, breaking after first
        $latest = $sxl->channel->item[0];

        // daily is <link> element
        $daily = (string) $latest->link;

        // image is in <description> -- /src="([^"]+)"
        $desc  = (string) $latest->description;
        if (!preg_match('/src="(?P<src>[^"]+)"/', $desc, $matches)) {
            return $this->registerError(sprintf(
                static::$comics[$this->comicShortName] . ' feed does not include image description containing image URL: %s',
                $desc
            ));
        }
        $image = $matches['src'];

        $comic = new Comic(
            /* 'name'  => */ static::$comics[$this->comicShortName],
            /* 'link'  => */ $this->comicBase,
            /* 'daily' => */ $daily,
            /* 'image' => */ $image
        );

        return $comic;
    }

    protected function registerError($message)
    {
        $comic = new Comic(
            /* 'name'  => */ static::$comics[$this->comicShortName],
            /* 'link'  => */ $this->comicBase
        );
        $comic->setError($message);
        return $comic;
    }
}
