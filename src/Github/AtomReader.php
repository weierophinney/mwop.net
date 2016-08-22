<?php
namespace Mwop\Github;

use Mwop\Collection;
use Zend\Feed\Reader\Reader as FeedReader;

class AtomReader
{
    const ATOM_FORMAT = 'https://github.com/%s.atom';

    protected $filters = [];
    protected $limit = 5;
    protected $user;

    public function __construct(string $user)
    {
        $this->user  = $user;
    }

    public function setLimit(int $limit) : self
    {
        $this->limit = $limit;
        return $this;
    }

    public function addFilter(callable $filter)
    {
        $this->filters[] = $filter;
    }

    public function read() : array
    {
        $url  = sprintf(self::ATOM_FORMAT, $this->user);
        $feed = FeedReader::import($url);

        $entries = Collection::create($feed)
            ->filterChain($this->filters)
            ->slice($this->limit)
            ->map(function ($entry) {
                return [
                    'title' => $entry->getTitle(),
                    'link'  => $entry->getLink(),
                ];
            });

        return [
            'last_modified' => $feed->getDateModified(),
            'link'          => $feed->getLink(),
            'links'         => $entries->toArray(),
        ];
    }
}
