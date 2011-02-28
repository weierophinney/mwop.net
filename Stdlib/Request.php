<?php

namespace Zend\Stdlib;

use Fig\Request as RequestDescription;

class Request extends Message implements RequestDescription
{
    public function __toString()
    {
        $request = '';
        foreach ($this->getMetadata() as $key => $value) {
            $request .= sprintf(
                "%s: %s\r\n",
                (string) $key,
                (string) $value
            );
        }
        $request .= "\r\n" . $this->getContent();

    }

    public function fromString($string)
    {
        throw new \DomainException('Unimplemented: ' . __METHOD__);
    }
}
