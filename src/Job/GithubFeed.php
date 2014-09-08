<?php
namespace Mwop\Job;

use ZendJobQueue;

class GithubFeed
{
    public function __invoke($req, $res, $next)
    {
        if (! ZendJobQueue::getCurrentJobId()) {
            $res->setStatusCode(403);
            $res->end();
            return;
        }

        $php     = \Mwop\getPhpExecutable();
        $command = $php . ' bin/mwop.net.php github-links';
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
        $uri    = (string) $req->getUrl()->setPath('/');
        $queue  = new ZendJobQueue();
        $queue->createHttpJob($uri . 'jobs/clear-cache', [], [
            'name'       => 'clear-cache',
            'persistent' => false,
        ]);
    }
}
