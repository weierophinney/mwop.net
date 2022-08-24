<?php

declare(strict_types=1);

namespace Mwop\Github;

use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\Mapper\TreeMapper;
use Laminas\Feed\Reader\Entry\EntryInterface;
use Laminas\Feed\Reader\Reader as FeedReader;

use function sprintf;

class AtomReader
{
    private const ATOM_FORMAT = 'https://github.com/%s.atom';

    protected array $filters = [];
    protected int $limit     = 10;

    public function __construct(
        protected string $user,
        private TreeMapper $mapper,
    ) {
    }

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function addFilter(callable $filter)
    {
        $this->filters[] = $filter;
    }

    public function read(): array
    {
        $url  = sprintf(self::ATOM_FORMAT, $this->user);
        $feed = FeedReader::import($url);

        $entries = AtomCollection::make($feed)
            ->filterChain($this->filters)
            ->slice(0, $this->limit)
            ->map(fn (EntryInterface $entry): AtomEntry => $this->mapper->map(AtomEntry::class, Source::array([
                'title'   => $entry->getTitle(),
                'link'    => $entry->getLink(),
                'content' => $entry->getContent(),
            ])));

        return [
            'last_modified' => $feed->getDateModified(),
            'link'          => $feed->getLink(),
            'links'         => $entries,
        ];
    }
}
