<?php
namespace Mwop\Factory;

use Zend\Http\Client as ZendHttpClient;

class HttpClient
{
    public function __invoke($services)
    {
        $http = new ZendHttpClient();
        $http->setOptions([
            'adapter' => 'Zend\Http\Client\Adapter\Curl',
        ]);
        return $http;
    }
}
