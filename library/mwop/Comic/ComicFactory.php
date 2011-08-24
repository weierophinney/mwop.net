<?php

namespace mwop\Comic;

use DomainException,
    InvalidArgumentException;

abstract class ComicFactory
{
    public static $comicClasses = array(
        'mwop\Comic\ComicSource\GoComics',
        'mwop\Comic\ComicSource\Dilbert',
        'mwop\Comic\ComicSource\ForBetterOrForWorse',
        'mwop\Comic\ComicSource\NotInventedHere',
        'mwop\Comic\ComicSource\UserFriendly',
        'mwop\Comic\ComicSource\Xkcd',
        'mwop\Comic\ComicSource\CtrlAltDel',
    );
    protected static $supported = array();

    public static function factory($name)
    {
        if (empty(static::$supported)) {
            static::initSupported();
        }

        if (!isset(static::$supported[$name])) {
            throw new InvalidArgumentException(sprintf(
                'Comic "%s" is not supported',
                $name
            ));
        }

        $class  = static::$supported[$name];
        $source = new $class($name);

        if (!$source instanceof ComicSource) {
            throw new DomainException(sprintf(
                'Comic "%s" does not have a valid ComicSource (uses "%s") associated with it',
                $name,
                $class
            ));
        }

        return $source;
    }

    protected static function initSupported()
    {
        foreach (static::$comicClasses as $class) {
            $supported = call_user_func($class . '::supports');
            foreach ($supported as $comic) {
                static::$supported[$comic] = $class;
            }
        }
    }
}

