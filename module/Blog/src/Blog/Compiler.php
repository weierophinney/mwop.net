<?php
namespace Blog;

use DateTime,
    DateTimezone,
    Iterator,
    Zend\Paginator\Paginator,
    Zend\Paginator\Adapter\Iterator as IteratorPaginator,
    Zend\Tag\Cloud as TagCloud;


class Compiler
{
    protected $byAuthor;
    protected $byDay;
    protected $byMonth;
    protected $byTag;
    protected $byYear;
    protected $entries;
    protected $files;

    public function __construct(Compiler\PhpFileFilter $files)
    {
        $this->files = $files;
    }

    public function compileEntryViewScripts()
    {
        $entries = $this->prepareEntries();

        foreach ($entries as $entry) {
            // We need:
            // - Location at which to write view script
            // - Template for view script
            //   - Does this mean a "renderer"?
        }
    }

    public function compilePaginatedEntries()
    {
        $entries   = $this->prepareEntries();
        $paginator = $this->getPaginator($entries);

        // We need:
        // - How many entries to include per page
        // - How many pages to show in the paginator
        // - Location at which to write view scripts
        // - Template for view script
        //   - Does this mean a "renderer"?
        // - Partial for paginator control
        //   - Does this mean a "renderer"?
        //
        // Then: 
        // - loop from page 1 to last page
        // - Generate each page

        /*
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(10);
        $paginator->setPageRange(10);
         */

    }

    public function compilePaginatedEntriesByDate()
    {
        $entries   = $this->prepareEntries();

        // We need:
        // - How many entries to include per page
        // - How many pages to show in the paginator
        // - Location at which to write view scripts
        // - Template for view script
        //   - Does this mean a "renderer"?
        // - Partial for paginator control
        //   - Does this mean a "renderer"?
        //
        // Then: 
        // - Filter entries by year
        //   - within years, filter entries by month
        //     - within month, filter entries by day
        //   {years: {
        //      2012: {
        //          entries: {
        //          }
        //          months: {
        //              03: {
        //                  entries: {
        //                  }
        //                  days: {
        //                      17: {
        //                          // entries
        //                      }
        //                  }
        //              }
        //          }
        //      }
        //   }}
        // - loop from page 1 to last page for each criteria
        // - Generate each page
    }

    public function compilePaginatedEntriesByTag()
    {
        $entries   = $this->prepareEntries();

        // We need:
        // - How many entries to include per page
        // - How many pages to show in the paginator
        // - Location at which to write view scripts
        // - Template for view script
        //   - Does this mean a "renderer"?
        // - Partial for paginator control
        //   - Does this mean a "renderer"?
        //
        // Then: 
        // - loop from page 1 to last page for each criteria
        // - Generate each page

        $tags = array();
        foreach ($this->byTag as $tag => $list) {

// What's in this loop will be the same for all the compile methods.
// Only difference will be what template is used, and what file is written to
            
            // Get a paginator for this tag
            $paginator = $this->getPaginator($list);

            // Loop through pages
            $pageCount = count($paginator);
            for ($i = 1; $i <= $pageCount; $i++) {
                $paginator->setCurrentPageNumber($i);

                // Generate this page
            }
        }
    }

    /**
     * Compile a tag cloud from the entries
     *
     * 
     * @todo   Should this write the tag cloud markup to a file?
     * @param  string $tagUrlTemplate 
     * @param  array $cloudOptions 
     * @return TagCloud
     */
    public function compileTagCloud($tagUrlTemplate = '/blog/tag/%s', $cloudOptions = array())
    {
        $this->prepareEntries();

        $tags = array();
        foreach ($this->byTag as $tag => $list) {
            $tags[] = array(
                'title'   => $tag,
                'weight'  => count($list),
                'params'  => array(
                    'url' => sprintf($tagUrlTemplate, $tag),
                ),
            );
        }
        $options['tags'] = $tags;

        return new TagCloud($options);
    }

    /**
     * Prepare the list of entries
     * 
     * Loops through the filesystem tree, looking for PHP files
     * that return EntryEntity objects. For each returned, adds it
     * to:
     *
     * - $entries, which has all entries
     * - $byYear, a hash of year/SortedEntries pairs
     * - $byMonth, a hash of year-month/SortedEntries pairs
     * - $byDay, a hash of year-month-day/SortedEntries pairs
     * - $byTag, a hash of tag/SortedEntries pairs
     * - $byAuthor, a hash of author/SortedEntries pairs
     *
     * @return void
     */
    protected function prepareEntries()
    {
        if ($this->entries) {
            return $this->entries;
        }

        $this->entries  = new Compiler\SortedEntries();
        $this->byYear   = array();
        $this->byMonth  = array();
        $this->byDay    = array();
        $this->byTag    = array();
        $this->byAuthor = array();
        foreach ($this->files as $file) {
            $entry = include $file->getRealPath();
            if (!$entry instanceof EntryEntity) {
                continue;
            }

            // First, set in entries
            $timestamp = $entry->getCreated();
            $this->entries->insert($entry, $created);

            // Then, set in appropriate year, month, and day slots
            $date      = new DateTime();
            $date->setTimestamp($timestamp)
                 ->setTimezone(new DateTimezone($entry->getTimezone()));

            $year  = $date->format('Y');
            $month = $date->format('Y-m');
            $day   = $date->format('Y-m-d');

            if (!isset($this->byYear[$year])) {
                $this->byYear[$year] = new Compiler\SortedEntries();
            }
            $this->byYear[$year]->insert($entry, $created);

            if (!isset($this->byMonth[$month])) {
                $this->byMonth[$month] = new Compiler\SortedEntries();
            }
            $this->byMonth[$month]->insert($entry, $created);

            if (!isset($this->byDay[$day])) {
                $this->byDay[$day] = new Compiler\SortedEntries();
            }
            $this->byDay[$day]->insert($entry, $created);

            // Next, set in appropriate tag lists
            foreach ($entry->getTags() as $tag) {
                if (!isset($this->byTag[$tag])) {
                    $this->byTag[$tag] = new Compiler\SortedEntries();
                }
                $this->byTag[$tag]->insert($entry, $created);
            }

            // Finally, by author
            $author = $entry->getAuthor();
            if (!isset($this->byAuthor[$author])) {
                $this->byAuthor[$author] = new Compiler\SortedEntries();
            }
            $this->byAuthor[$author]->insert($entry, $created);
        }
    }

    /**
     * Retrieve configured paginator
     *
     * We need following configuration
     * - How many entries to include per page
     * - How many pages to show in the paginator
     * - Template for view script
     * - Partial for paginator control
     * 
     * @param  Iterator $it 
     * @return Paginator
     */
    protected function getPaginator(Iterator $it)
    {
        $paginator = new Paginator(new IteratorPaginator($it));
        return $paginator;
    }
}
