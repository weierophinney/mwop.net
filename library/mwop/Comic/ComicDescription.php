<?php

namespace mwop\Comic;

/**
 * Describes the pieces of a daily comic that will be used to generate output
 */
interface ComicDescription
{
    public function getName();
    public function getLink();
    public function getDaily();
    public function getImage();
}
