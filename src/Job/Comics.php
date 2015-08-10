<?php
namespace Mwop\Job;

use ZendJobQueue;

class Comics
{
    public function __invoke($req, $res, $next)
    {
        if (! class_exists('ZendJobQueue') || ! ZendJobQueue::getCurrentJobId()) {
            return $res
                ->wthStatus(403)
                ->end();
        }

        $php     = \Mwop\getPhpExecutable();
        $command = $php . ' vendor/phly/phly-comic/bin/phly-comic.php fetch-all --output=data/comics.mustache';
        exec($command, $output, $return);
        if ($return != 0) {
            ZendJobQueue::setCurrentJobStatus(ZendJobQueue::FAILED);
            return $res
                ->withStatus(500)
                ->withHeader('Content-Type', 'text/plain')
                ->end(implode("\n", $output));
        }

        ZendJobQueue::setCurrentJobStatus(ZendJobQueue::OK);

        // Clear caches
        $uri    = $req->getUri()->withPath('/');
        $queue  = new ZendJobQueue();
        $queue->createHttpJob($uri . 'jobs/clear-cache', [], [
            'name'       => 'clear-cache',
            'persistent' => false,
        ]);

        return $res
            ->withHeader('Content-Type', 'text/plain')
            ->end(implode("\n", $output));
    }
}
