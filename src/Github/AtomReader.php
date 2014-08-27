<?php
namespace Mwop\Github;

use Zend\Feed\Reader\Reader as FeedReader;

class AtomReader
{
    const ATOM_FORMAT = 'https://github.com/%s.private.actor.atom?token=%s';

    protected $filters = array();
    protected $limit = 5;
    protected $token;
    protected $user;

    public function __construct($user, $token)
    {
        $this->user  = $user;
        $this->token = $token;
    }

    public function setLimit($limit)
    {
        $this->limit = (int) $limit;
        return $this;
    }

    public function addFilter($filter)
    {
        if (!is_callable($filter)) {
            throw new \InvalidArgumentException(sprintf(
                '%s: expected a PHP callback; received "%s"',
                __METHOD__,
                (is_object($filter) ? get_class($filter) : gettype($filter))
            ));
        }
        $this->filters[] = $filter;
    }

    public function read()
    {
        $url  = sprintf(self::ATOM_FORMAT, $this->user, $this->token);
        $feed = FeedReader::import($url);

        $lastModified = $feed->getDateModified();
        $altLink      = $feed->getLink();
        $entries      = array();
        $i            = 0;

        foreach ($feed as $entry) {
            if (!$this->filter($entry)) {
                continue;
            }

            $data = array(
                'title'        => $entry->getTitle(),
                'link'         => $entry->getLink(),
            );

            $entries[] = $data;
            $i++;
            if ($i > $this->limit) {
                break;
            }
        }

        return array(
            'last_modified' => $lastModified,
            'link'          => $altLink,
            'links'         => $entries,
        );
    }

    /**
     * Filter an entry
     *
     * If a filter returns a boolean false, this method will
     * return boolean false, indicating not to include the
     * entry in the resultset, nor count it against the limit.
     * 
     * @param  \Zend\Feed\Reader\Entry $entry 
     * @return bool
     */
    protected function filter($entry)
    {
        foreach ($this->filters as $filter) {
            $result = call_user_func($filter, $entry);
            if (!$result) {
                return false;
            }
        }
        return true;
    }
}
