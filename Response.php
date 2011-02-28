<?php

namespace Fig;

interface Response extends Message
{
    public function __toString();
    public function fromString($string);

    // send? or emit?
    public function send();
}
