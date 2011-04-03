<?php
namespace Blog\View;

class Entry
{
    protected $entry;
    protected $request;

    public function __construct(array $data)
    {
        if (!isset($data['entity'])) {
            throw new \DomainException('Expected entity; did not receive one');
        }
        $entry = $data['entity'];

        $this->entry = $entry;
        $this->id    = $entry->getId();
        $this->title = $entry->getTitle();
        $this->body = $entry->getBody();
        $this->extended = $entry->getExtended();
        $this->author = $entry->getAuthor();

        if (isset($data['request'])) {
            $this->request = $data['request'];
        }
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
        $tags = array();
        foreach ($this->entry->getTags() as $tag) {
            $tag = htmlspecialchars($tag, ENT_COMPAT, "UTF-8");
            $tags[] = sprintf('<a href="/blog/tag/%s">%s</a>', $tag, $tag);
        }
        return implode('&nbsp;|&nbsp;', $tags);
    }

    protected function getDateString($ts)
    {
        $tz = $this->entry->getTimezone();
        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone($tz));
        $date->setTimestamp($ts);
        return $date->format('Y-m-d H:i:s');
    }
}
