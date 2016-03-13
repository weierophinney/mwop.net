<?php
namespace Mwop\Blog;

use DateTime;
use DateTimezone;

class EntryView
{
    private $created;
    private $updated;
    private $tags = [];
    private $tagsProcessed = false;

    public $body;
    public $disqus;
    public $extended;
    public $id;
    public $title;
    public $uriHelper;

    public function __construct(array $entry, array $disqus = [])
    {
        $this->disqus   = $disqus;

        foreach ($entry as $key => $value) {
            switch ($key) {
                case 'body':
                case 'created':
                case 'extended':
                case 'id':
                case 'tags':
                case 'title':
                case 'updated':
                case 'uriHelper':
                    $this->{$key} = $value;
                    break;
                default:
                    break;
            }
        }
    }

    public function created()
    {
        return $this->formatDate($this->created);
    }

    // @codingStandardsIgnoreStart
    public function created_rfc()
    {
        return $this->formatDateRfc($this->created);
    }
    // @codingStandardsIgnoreEnd

    public function updated()
    {
        if (! $this->updated || $this->updated === $this->created) {
            return false;
        }

        return [
            'rfc'  => $this->formatDateRfc($this->updated),
            'when' => $this->formatDate($this->updated),
        ];
    }

    public function uri()
    {
        $factory = $this->uriHelper;
        return $factory();
    }

    public function url()
    {
        $uriMetadata = json_encode([
            'name'    => 'blog.post',
            'options' => ['id' => $this->id],
        ]);
        $generator = $this->uri();
        $path      = $generator($uriMetadata, function ($text) {
            return $text;
        });

        return sprintf('https://mwop.net%s', $path);
    }

    public function tags()
    {
        if (! $this->tagsProcessed) {
            $this->marshalTags();
        }

        return $this->tags;
    }

    public function marshalTags()
    {
        $this->tagsProcessed = true;
        $generator = $this->uri();
        $tags      = $this->tags;
        $renderer  = function ($text) {
            return $text;
        };

        if (! is_array($tags)) {
            $tags = explode('|', trim((string) $tags, '|'));
        }

        $tags = array_map(function ($tag) use ($generator, $renderer) {
            $linkData = json_encode([
                'name'    => 'blog.tag',
                'options' => ['tag' => $tag],
            ]);
            $atomData = json_encode([
                'name'    => 'blog.tag.feed',
                'options' => ['tag' => $tag, 'type' => 'atom'],
            ]);
            $rssData = json_encode([
                'name'    => 'blog.tag.feed',
                'options' => ['tag' => $tag, 'type' => 'rss'],
            ]);

            return [
                'tag'  => $tag,
                'link' => $generator($linkData, $renderer),
                'atom' => $generator($atomData, $renderer),
                'rss'  => $generator($rssData, $renderer),
            ];
        }, $tags);

        $this->tags = array_values($tags);
    }

    private function formatDate($dateString)
    {
        if (is_numeric($dateString)) {
            $date = new DateTime('@' . $dateString, new DateTimezone('America/Chicago'));
        } else {
            $date = new DateTime($dateString);
        }
        return $date->format('j F Y');
    }

    private function formatDateRfc($dateString)
    {
        if (is_numeric($dateString)) {
            $date = new DateTime('@' . $dateString, new DateTimezone('America/Chicago'));
        } else {
            $date = new DateTime($dateString);
        }
        return $date->format('c');
    }
}
