<?php
namespace Mwop\Job;

use ZendJobQueue;

class Comics
{
    public function __invoke($req, $res, $next)
    {
        if (! ZendJobQueue::getCurrentJobId()) {
            $res->setStatusCode(403);
            $res->end();
            return;
        }

        $command = '/usr/local/zend/bin/php -d date.timezone=America/Chicago vendor/phly/phly-comic/bin/phly-comic.php fetch-all';
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
