<?php
namespace Mwop\Blog;

use Zend\Console\ColorInterface as Color;
use Zend\Feed\Writer\Feed as FeedWriter;

class FeedGenerator
{
    private $console;

    private $defaultAuthor = [
        'name'  => 'Matthew Weier O\'Phinney',
        'email' => 'me@mwop.net',
        'uri'   => 'http://mwop.net',
    ];

    private $mapper;

    public function __construct(MapperInterface $mapper)
    {
        $this->mapper = $mapper;
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

        $latest = false;
        $posts->setCurrentPageNumber(1);
        foreach ($posts as $details) {
            $post = include $details['path'];
            if (! $post instanceof EntryEntity) {
                $this->console->write('Invalid post detected: ', Color::RED);
                $this->console->writeLine($details['path']);
                continue;
            }

            if (! $latest) {
                $latest = $post;
            }

            $authorDetails = $this->defaultAuthor;
            $author        = $post->getAuthor();
            if ($author instanceof AuthorEntity && $author->isValid()) {
                $authorDetails = array(
                    'name'  => $author->getName(),
                    'email' => $author->getEmail(),
                    'uri'   => $author->getUrl(),
                );
            }

            $entry = $feed->createEntry();
            $entry->setTitle($post->getTitle());
            $entry->setLink(sprintf('%s/%s.html', $baseUri, $post->getId()));

            $entry->addAuthor($authorDetails);
            $entry->setDateModified($post->getUpdated());
            $entry->setDateCreated($post->getCreated());
            $entry->setContent($post->getBody() . $post->getExtended());

            $feed->addEntry($entry);
        }

        // Set feed date
        $feed->setDateModified($latest->getUpdated());

        // Write feed to file
        $file = sprintf('%s%s.xml', $fileBase, $type);
        $file = str_replace(' ', '+', $file);
        file_put_contents($file, $feed->export($type));
    }
}
