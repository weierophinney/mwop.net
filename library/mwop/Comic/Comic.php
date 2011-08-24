<?php

namespace mwop\Comic;

/**
 * Value object describing a comic
 */
class Comic implements ComicDescription
{
    protected $name;
    protected $link;
    protected $daily;
    protected $image;

    public function __construct($name, $link, $daily, $image)
    {
        $this->name  = $name;
        $this->link  = $link;
        $this->daily = $daily;
        $this->image = $image;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function getDaily()
    {
        return $this->daily;
    }

    public function getImage()
    {
        return $this->image;
    }
}
