<?php
namespace Blog;

use Iterator,
    Zend\Paginator\Paginator,
    Zend\Paginator\Adapter\Iterator as IteratorPaginator;


class Compiler
{
    protected $entries;
    protected $files;

    public function __construct(Compiler\PhpFileFilter $files)
    {
        $this->files = $files;
    }

    public function compileEntryViewScripts()
    {
        $entries = $this->getEntries();

        foreach ($entries as $entry) {
            // We need:
            // - Location at which to write view script
            // - Template for view script
            //   - Does this mean a "renderer"?
        }
    }

    public function compilePaginatedEntries()
    {
        $entries   = $this->getEntries();
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
        $entries   = $this->getEntries();

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
        $entries   = $this->getEntries();

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
        // - Filter entries by tag
        //   {
        //      zf:  [...]
        //      php: [...]
        //   }
        // - loop from page 1 to last page for each criteria
        // - Generate each page
    }

    public function compileTagCloud()
    {
        $entries = $this->getEntries();

        // We need:
        // - Location at which to write view scripts
        // - Template for view script
        //   - Does this mean a "renderer"?
        // Then:
        // - Foreach entry:
        //   - Get tags
        //     - Create new "tag" key if needed
        //     - Increment count for tag by 1
        // - Generate tag cloud
    }

    protected function getEntries()
    {
        if ($this->entries) {
            return $this->entries;
        }

        $entries = new Compiler\SortedEntries();
        foreach ($this->files as $file) {
            $entry = include $file->getRealPath();
            if (!$entry instanceof EntryEntity) {
                continue;
            }
            $entries->insert($entry, $entry->getCreated());
        }
        $this->entries = $entries;
        return $this->entries;
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
