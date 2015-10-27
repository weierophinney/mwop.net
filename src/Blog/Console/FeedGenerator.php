<?php
namespace Mwop\Blog\Console;

use DateTime;
use Mni\FrontYAML\Bridge\CommonMark\CommonMarkParser;
use Mni\FrontYAML\Parser;
use Mwop\Blog;
use Symfony\Component\Yaml\Parser as YamlParser;
use Zend\Console\ColorInterface as Color;
use Zend\Feed\Writer\Feed as FeedWriter;

class FeedGenerator
{
    private $authors = [];

    private $authorsPath;

    private $console;

    private $defaultAuthor = [
        'name'  => 'Matthew Weier O\'Phinney',
        'email' => 'me@mwop.net',
        'uri'   => 'http://mwop.net',
    ];

    private $mapper;

    public function __construct(Blog\MapperInterface $mapper, $authorsPath)
    {
        $this->mapper      = $mapper;
        $this->authorsPath = $authorsPath;
    }

    public function __invoke($route, $console)
    {
        $this->console = $console;
        $outputDir = $route->getMatchedParam('outputDir');
        $baseUri   = $route->getMatchedParam('baseUri');

        $this->console->writeLine('Generating base feeds');
        $this->generateFeeds(
            $outputDir . '/',
            'Blog entries :: phly, boy, phly',
            $baseUri,
            $baseUri,
            $this->mapper->fetchAll()
        );

        $cloud = $this->mapper->fetchTagCloud();
        $tags  = array_map(function ($item) {
            return $item->getTitle();
        }, iterator_to_array($cloud->getItemList()));

        foreach ($tags as $tag) {
            if (empty($tag)) {
                continue;
            }

            $this->console->writeLine('Generating feeds for tag ' . $tag);
            $this->generateFeeds(
                sprintf('%s/%s.', $outputDir, $tag),
                sprintf('Tag: %s :: phly, boy, phly', $tag),
                $baseUri,
                sprintf('%s/tag/%s', $baseUri, str_replace(' ', '+', $tag)),
                $this->mapper->fetchAllByTag($tag)
            );
        }
    }

    private function generateFeeds($fileBase, $title, $baseUri, $feedUri, $posts)
    {
        foreach (['atom', 'rss'] as $type) {
            $this->generateFeed($type, $fileBase, $title, $baseUri, $feedUri, $posts);
        }
    }

    private function generateFeed($type, $fileBase, $title, $baseUri, $feedUri, $posts)
    {
        $feed = new FeedWriter();
        $feed->setTitle($title);
        $feed->setLink($feedUri);
        $feed->setFeedLink(sprintf('%s/%s.xml', $feedUri, $type), $type);

        if ($type === 'rss') {
            $feed->setDescription($title);
        }

        $parser = new Parser(null, new CommonMarkParser());
        $latest = false;
        $posts->setCurrentPageNumber(1);
        foreach ($posts as $details) {
            $document = $parser->parse(file_get_contents($details['path']));
            $post     = $document->getYAML();
            $html     = $document->getContent();
            $author   = $this->getAuthor($post['author']);

            if (! $latest) {
                $latest = $post;
            }

            $entry = $feed->createEntry();
            $entry->setTitle($post['title']);
            $entry->setLink(sprintf('%s/%s.html', $baseUri, $post['id']));

            $entry->addAuthor($author);
            $entry->setDateModified(new DateTime($post['updated']));
            $entry->setDateCreated(new DateTime($post['created']));
            $entry->setContent($html);

            $feed->addEntry($entry);
        }

        // Set feed date
        $feed->setDateModified(new DateTime($latest['updated']));

        // Write feed to file
        $file = sprintf('%s%s.xml', $fileBase, $type);
        $file = str_replace(' ', '+', $file);
        file_put_contents($file, $feed->export($type));
    }

    /**
     * Retrieve author metadata.
     *
     * @param string $author
     * @return string[]
     */
    private function getAuthor($author)
    {
        if (isset($this->authors[$author])) {
            return $this->authors[$author];
        }
        
        $path = sprintf('%s/%s.yml', $this->authorsPath, $author);
        if (! file_exists($path)) {
            $this->authors[$author] = $this->defaultAuthor;
            return $this->authors[$author];
        }

        $this->authors[$author] = (new YamlParser())->parse(file_get_contents($path));
        return $this->authors[$author];
    }
}
