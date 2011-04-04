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

        $this->entry    = $entry;
        $this->id       = $entry->getId();
        $this->title    = $entry->getTitle();
        $this->body     = $entry->getBody();
        $this->extended = $entry->getExtended();
        $this->author   = $entry->getAuthor();

        if (isset($data['request'])) {
            $this->request = $data['request'];
        }

        $requires =<<<EOJ
        dojo.require("dojox.highlight");
        dojo.require("dojox.highlight.languages._all");
        dojo.require("dojox.highlight.languages.pygments.css");
        dojo.addOnLoad(function() {
            dojo.query("code").forEach(dojox.highlight.init);
        });
EOJ;

        $this->layout = array(
            'js' => array(
                'source' => array(
                    array('code' => $requires),
                )
            ),
            'css' => array(
                array('url' => 'http://ajax.googleapis.com/ajax/libs/dojo/1.6/dojox/highlight/resources/highlight.css'),
                array('url' => 'http://ajax.googleapis.com/ajax/libs/dojo/1.6/dojox/highlight/resources/pygments/autumn.css'),
            ),
        );
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
