<?php
namespace Mwop\Job;

use Exception;
use Mwop\Github;
use ZendJobQueue;
use Zend\Escaper\Escaper;

class GithubFeed
{
    /**
     * File to which to write feed.
     */
    private $outputFile;

    /**
     * @var string
     */
    private $outputTemplateString;

    /**
     * @var Github\AtomReader
     */
    private $reader;

    /**
     * @param Github\AtomReader $reader
     * @param string $outputFile
     * @param string $outputTemplateString
     */
    public function __construct(
        Github\AtomReader $reader,
        $outputFile = 'data/github-links.mustache',
        $outputTemplateString = '<li><a href="%s">%s</a></li>'
    ) {
        $this->reader               = $reader;
        $this->outputFile           = $outputFile;
        $this->outputTemplateString = $outputTemplateString;
    }

    public function __invoke($req, $res, $next)
    {
        if (! class_exists('ZendJobQueue') || ! ZendJobQueue::getCurrentJobId()) {
            return $res->withStatus(403);
        }

        printf("Retrieving GitHub activity links\n");

        try {
            $data = $this->reader->read();
        } catch (Exception $e) {
            ZendJobQueue::setCurrentJobStatus(ZendJobQueue::FAILED);
            $res->getBody()->write(implode("\n", $output));
            return $res
                ->withStatus(500)
                ->withHeader('Content-Type', 'text/plain');
        }

        file_put_contents(
            $this->outputFile,
            $this->createContentFromData(
                $data,
                $this->outputTemplateString
            )
        );

        ZendJobQueue::setCurrentJobStatus(ZendJobQueue::OK);

        // Clear caches
        $uri    = $req->getUri()->withPath('/');
        $queue  = new ZendJobQueue();
        $queue->createHttpJob($uri . 'jobs/clear-cache', [], [
            'name'       => 'clear-cache',
            'persistent' => false,
        ]);

        $res->getBody()->write("[DONE] Retrieving GitHub activity links\n");
        return $res->withHeader('Content-Type', 'text/plain');
    }

    /**
     * Create content to write to the output file
     *
     * Uses the passed data and template to generate content.
     *
     * @param array $data
     * @param string $template
     * @return string
     */
    private function createContentFromData($data, $template)
    {
        $escaper = new Escaper();
        $strings = array_map(function ($link) use ($template, $escaper) {
            return sprintf(
                $template,
                $link['link'],
                $escaper->escapeHtml($link['title'])
            );
        }, $data['links']);
        return implode("\n", $strings);
    }
}
