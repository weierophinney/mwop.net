<?php

namespace Comic;

/**
 * Describes a comic source
 */
interface ComicSource
{
    public static function supports();
    public function fetch();
    public function getError();
}
