<?php

namespace Comic\ComicSource;

use Comic\Comic,
    Zend\Dom\Query as DomQuery;


abstract class AbstractDomSource extends AbstractComicSource
{
    /**
     * @var string URL to comic landing page
     */
    protected $comicBase;

    /**
     * @var string short name of comic
     */
    protected $comicShortName;

    /**
     * @var string sprintf() string indicating URL format
     */
    protected $dailyFormat = '%s';

    /**
     * @var string Date format as a substitution in the {@link $dailyFormat}
     */
    protected $dateFormat = 'Y/m/d';

    /**
     * @var string CSS query string describing location of image in page
     */
    protected $domQuery = '';

    /**
     * @var bool Is the DOM structure HTML?
     */
    protected $domIsHtml = false;

    /**
     * @var bool Use the comicBase instead of the daily format to retrieve
     */
    protected $useComicBase = false;

    public function fetch()
    {
        if ($this->useComicBase) {
            $url  = $this->comicBase;
        } else {
            $url  = sprintf($this->dailyFormat, date($this->dateFormat));
        }
        $page = file_get_contents($url);
        if (!$page) {
            return $this->registerError(sprintf(
                'Comic at "%s" is unreachable',
                $url
            ));
        }

        $dom  = new DomQuery();
        if ($this->domIsHtml) {
            $dom->setDocumentHtml($page);
        } else {
            $dom->setDocument($page);
        }

        $r = $dom->execute($this->domQuery);
        if (!$r->count()) {
            return $this->registerError(sprintf(
                'Comic at "%s" is unreachable',
                $url
            ));
        }

        $imgUrl = false;
        foreach ($r as $node) {
            if ($node->hasAttribute('src')) {
                $src = $node->getAttribute('src');
                if ($this->validateImageSrc($src)) {
                    $imgUrl = $this->formatImageSrc($src);
                    break;
                }
            }
        }

        if (!$imgUrl) {
            return $this->registerError(sprintf(
                'Unable to find image source in "%s"',
                $url
            ));
        }

        if (!($dailyUrl = $this->getDailyUrl($imgUrl))) {
            $dailyUrl = $url;
        }

        $comic = new Comic(
            /* 'name'  => */ static::$comics[$this->comicShortName],
            /* 'link'  => */ $this->comicBase,
            /* 'daily' => */ $dailyUrl,
            /* 'image' => */ $imgUrl
        );

        return $comic;
    }

    protected function validateImageSrc($src)
    {
        return true;
    }

    protected function formatImageSrc($src)
    {
        return $src;
    }

    protected function getDailyUrl($imgUrl)
    {
        return false;
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
