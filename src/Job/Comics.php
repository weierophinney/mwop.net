<?php
namespace Mwop\Job;

use Phly\Http\Uri;
use ZendJobQueue;

class Comics
{
    public function __invoke($req, $res, $next)
    {
        if (! class_exists('ZendJobQueue') || ! ZendJobQueue::getCurrentJobId()) {
            $res->setStatusCode(403);
            $res->end();
            return;
        }

        $php     = \Mwop\getPhpExecutable();
        $command = $php . ' vendor/phly/phly-comic/bin/phly-comic.php fetch-all --output=data/comics.mustache';
        exec($command, $output, $return);
        if ($return != 0) {
            ZendJobQueue::setCurrentJobStatus(ZendJobQueue::FAILED);
            $res->setStatusCode(500);
            $res->addHeader('Content-Type', 'text/plain');
            $res->end(implode("\n", $output));
            return;
        }

        ZendJobQueue::setCurrentJobStatus(ZendJobQueue::OK);
        $res->addHeader('Content-Type', 'text/plain');
        $res->end(implode("\n", $output));

        // Clear caches
        $uri    = new Uri($req->getUrl());
        $uri    = (string) $uri->setPath('/');
        $queue  = new ZendJobQueue();
        $queue->createHttpJob($uri . 'jobs/clear-cache', [], [
            'name'       => 'clear-cache',
            'persistent' => false,
        ]);
    }
}
