<?php

namespace mwop\Comic\ComicSource;

use mwop\Comic\ComicSource;

/**
 * Provides shared functionality for most comic source classes
 */
abstract class AbstractComicSource implements ComicSource
{
    /**
     * Assoc array of shortname => title pairs detailing supported comics
     */
    protected static $comics = array();

    protected $error = false;

    public static function supports()
    {
        return array_keys(static::$comics);
    }

    protected function registerError($message)
    {
        $this->error = $message;
    }

    public function getError()
    {
        return $this->error;
    }
}
