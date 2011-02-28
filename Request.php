<?php

namespace Fig;

interface Request extends Message
{
    public function __toString();
    public function fromString($string);
}
