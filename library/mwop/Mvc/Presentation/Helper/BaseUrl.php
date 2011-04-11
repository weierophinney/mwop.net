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
            $this->baseUrl = $this->request->getBaseUrl();
        }
        return $this->baseUrl;
    }
}
