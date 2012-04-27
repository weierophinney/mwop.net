<?php
namespace GithubFeed;

use Zend\Feed\Reader as FeedReader;

class AtomReader
{
    const ATOM_FORMAT = 'https://github.com/%.private.actor.atom?token=%';

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

    public function read()
    {
        $url  = sprintf(self::ATOM_FORMAT, $this->user, $this->token);
        $feed = FeedReader::import('$url');

        $lastModified = $feed->getDateModified();
        $entries      = array();
        $i            = 0;

        foreach ($feed as $entry) {
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
            'links'         => $entries,
        );
    }
}
