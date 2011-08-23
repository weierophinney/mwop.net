<?php
namespace Blog\View;

use mwop\Mvc\Presentation;

class Entry
{
    protected $entry;
    protected $entryUrl;
    protected $presentation;
    protected $request;

    public function __construct(array $data)
    {
        if (!isset($data['entity'])) {
            throw new \DomainException('Expected entity; did not receive one');
        }
        $entry = $data['entity'];

        $this->entry    = $entry;
        $this->id       = $entry->getId();
        $this->title    = $entry->getTitle();
        $this->body     = $entry->getBody();
        $this->extended = $entry->getExtended();
        $this->author   = $entry->getAuthor();

        if (isset($data['request'])) {
            $this->request = $data['request'];
        }
    }

    public function setPresentation(Presentation $presentation)
    {
        $this->presentation = $presentation;
        Layout::setup($presentation);
    }

    public function disqusKey()
    {
        return $this->presentation->disqusKey;
    }

    public function url()
    {
        if (isset($this->entryUrl)) {
            return $this->entryUrl;
        }
        if (!$this->presentation) {
            $this->entryUrl = '';
        } else {
            $this->entryUrl = $this->presentation->helper('url')->generate(array('id' => $this->id), array('name' => 'blog-entry'));
        }
        return $this->entryUrl;
    }

    public function created()
    {
        return $this->getDateString($this->entry->getCreated());
    }

    public function updated()
    {
        return $this->getDateString($this->entry->getUpdated());
    }

    public function tags()
    {
        $base = '';
        $tags = array();
        if (null !== $this->presentation) {
            $base   = $this->presentation->helper('request')->getBaseUrl();
            $router = $this->presentation->helper('router');
            foreach ($this->entry->getTags() as $tag) {
                $tags[] = sprintf('<a href="%s">%s</a>', $base . $router->assemble(array('tag' => $tag), array('name' => 'blog-tag')), $tag);
            }
        } else {
            foreach ($this->entry->getTags() as $tag) {
                $tag = htmlspecialchars($tag, ENT_COMPAT, "UTF-8");
                $tags[] = sprintf('<a href="/blog/tag/%s">%s</a>', $tag, $tag);
            }
        }
        return implode(', ', $tags);
    }


    protected function getDateString($ts)
    {
        $tz = $this->entry->getTimezone();
        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone($tz));
        $date->setTimestamp($ts);
        return $date->format('Y-m-d H:i:s');
    }

    public function author_url()
    {
        $base = '';
        if (null !== $this->presentation) {
            $base = $this->presentation->helper('request')->getBaseUrl();
        }
        return $base . '/blog/author/' . $this->author;
    }
}
