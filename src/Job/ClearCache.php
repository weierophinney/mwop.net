<?php
namespace Mwop\Job;

use Phly\Http\Uri;
use ZendJobQueue;

class ClearCache
{
    private $rules = [
        'mwop_home'   => '/',
        'mwop_resume' => '/resume',
    ];

    public function __invoke($req, $res, $next)
    {
        if (! class_exists('ZendJobQueue') || ! ZendJobQueue::getCurrentJobId()) {
            $res->setStatusCode(403);
            $res->end();
            return;
        }

        // Uri removes ports when they are the default for that scheme.
        // However, ZS page cache REQUIRES the port in order to properly
        // match. This logic gives us a base URI string to use for
        // clearing cache contents.
        $uri  = new Uri($req->getUrl());
        $port = $uri->port;
        if (! $port) {
            $port = ($uri->scheme === 'https') ? 443 : 80;
        }
        $uri  = sprintf('%s://%s:%d', $uri->scheme, $uri->host, $port);

        foreach ($this->rules as $rule => $path) {
            // Cannot use the more specific page_cache_remove_cached_contents_by_uri()
            // as it does not appear to work for any combination of criteria. The
            // following works reliably.
            page_cache_remove_cached_contents(
                $uri . $path
            );
        }

        ZendJobQueue::setCurrentJobStatus(ZendJobQueue::OK);
        $res->end();
    }
}
