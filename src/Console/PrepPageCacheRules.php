<?php
namespace Mwop\Console;

use Zend\Diactoros\Uri;

class PrepPageCacheRules
{
    private $dist = 'zpk/scripts/pagecache_rules.xml.dist';

    private $xml = 'zpk/scripts/pagecache_rules.xml';

    public function __invoke($route, $console)
    {
        $appId = $route->getMatchedParam('appId');
        $site  = $route->getMatchedParam('site');

        $uri = new Uri(trim($site, '/') . '/');
        $port = $uri->getPort();
        if (! $port) {
            $port = ($uri->getScheme() == 'https') ? 443 : 80;
        }

        $rules = file_get_contents($this->dist);
        $rules = str_replace('%SCHEME%', $uri->getScheme(), $rules);
        $rules = str_replace('%HOST%', $uri->getHost(), $rules);
        $rules = str_replace('%PORT%', $port, $rules);
        $rules = str_replace('%APPID%', $appId, $rules);

        file_put_contents($this->xml, $rules);
        return 0;
    }
}
