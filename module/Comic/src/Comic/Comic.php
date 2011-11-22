<?php

namespace Comic;

/**
 * Value object describing a comic
 */
class Comic implements ComicDescription
{
    protected $name;
    protected $link;
    protected $daily;
    protected $image;
    protected $error;

    public function __construct($name, $link = null, $daily = null, $image = null)
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

    public function setError($error)
    {
        $this->error = $error;
    }

    public function hasError()
    {
        return (null !== $this->error);
    }

    public function getError()
    {
        return $this->error;
    }
}
