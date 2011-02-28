<?php

namespace Fig\Http;

use Fig\Response;

interface HttpResponse extends Response
{
    public function __construct($content = '', $status = 200, $headers = null);

    public function sendHeaders();
    public function sendContent();
    // public function send(); // send both headers and content; defined in Fig\Response

    /* mutators and accessors */
    public function getHeaders();
    public function setHeaders(HttpResponseHeaders $headers);
}
