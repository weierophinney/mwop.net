<?php

namespace Fig;

interface Message
{
    public function setMetadata($spec, $value = null);
    public function getMetadata($key = null);

    public function setContent($content);
    public function getContent();
}
