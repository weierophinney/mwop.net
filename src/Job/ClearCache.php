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
        if (! ZendJobQueue::getCurrentJobId()) {
            $res->setStatusCode(403);
            $res->end();
            return;
        }

        $uri = $req->getUrl();
        $uri = Uri::fromArray([
            'scheme' => $uri->scheme,
            'host'   => $uri->host,
            'port'   => $uri->port,
            'path'   => $uri->path,
        ]);

        foreach ($this->rules as $rule => $path) {
            $uri = $uri->setPath($path);
            page_cache_remove_cached_contents_by_uri(
                $rule,
                (string) $uri
            );
        }

        ZendJobQueue::setCurrentJobStatus(ZendJobQueue::OK);
        $res->end();
    }
}
