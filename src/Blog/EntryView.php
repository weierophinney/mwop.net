<?php
namespace Mwop\Blog;

use DateTime;
use DateTimezone;
use Phly\Http\Uri;

class EntryView
{
    private $basePath;
    private $created;
    private $updated;

    public $body;
    public $disqus;
    public $extended;
    public $id;
    public $tags;
    public $title;

    public function __construct(array $entry, $basePath, array $disqus)
    {
        $this->basePath = $basePath;
        $this->disqus   = $disqus;

        foreach ($entry as $key => $value) {
            switch ($key) {
                case 'body':
                case 'created':
                case 'extended':
                case 'id':
                case 'title':
                case 'updated':
                    $this->{$key} = $value;
                    break;
                case 'tags':
                    $this->tags = $this->marshalTags($value);
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

    public function updated()
    {
        if (! $this->updated || $this->updated === $this->created) {
            return false;
        }

        return [
            'when' => $this->formatDate($this->updated),
        ];
    }

    public function path()
    {
        return sprintf('%s/%s.html', $this->basePath, $this->id);
    }

    public function url()
    {
        $uri = Uri::fromArray([
            'host'   => 'mwop.net',
            'path'   => sprintf('/blog/%s.html', $this->id),
        ]);
        return (string) $uri;
    }

    private function marshalTags($tags)
    {
        $basePath = $this->basePath;

        if (! is_array($tags)) {
            $tags = explode('|', trim((string) $tags, '|'));
        }

        return array_map(function ($tag) use ($basePath) {
            return [
                'tag'  => $tag,
                'link' => sprintf('%s/tag/%s', $basePath, $tag),
            ];
        }, $tags);
    }

    private function formatDate($timestamp)
    {
        $date = new DateTime('@' . $timestamp, new DateTimezone('America/Chicago'));
        return $date->format('j F Y');
    }
}
