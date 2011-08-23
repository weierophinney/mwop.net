<?php
namespace mwop\Mvc\Presentation\Helper;

use Fig\Request;

class BaseUrl
{
    protected $baseUrl;
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function get()
    {
        if (null === $this->baseUrl) {
            $this->baseUrl = 'http://' . $_SERVER['HTTP_HOST'] . $this->request->getBaseUrl();
        }
        return $this->baseUrl;
    }
}
