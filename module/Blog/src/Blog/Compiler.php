<?php
namespace Blog;

use DateTime,
    DateTimezone,
    Iterator,
    stdClass,
    Zend\Paginator\Paginator,
    Zend\Paginator\Adapter\Iterator as IteratorPaginator,
    Zend\Tag\Cloud as TagCloud,
    Zend\View\Model\ViewModel,
    Zend\View\View;


class Compiler
{
    protected $byAuthor;
    protected $byDay;
    protected $byMonth;
    protected $byTag;
    protected $byYear;
    protected $entries;
    public    $filename;
    protected $files;
    protected $responseStrategyPrepared = false;
    protected $tagCloud;
    protected $view;

    public function __construct(Compiler\PhpFileFilter $files, View $view)
    {
        $this->files = $files;
        $this->view  = $view;
    }

    public function compileEntryViewScripts($template, $filenameTemplate = '%s.html')
    {
        $entries = $this->prepareEntries();

        foreach ($entries as $entry) {
            $filename = sprintf($filenameTemplate, $entry->getId());
            $this->prepareResponseStrategy($filename);

            $model = new ViewModel(array(
                'entry' => $entry,
            ));
            $model->setTemplate($template);

            $this->view->render($model);
        }
    }

    public function compilePaginatedEntries($template, $filenameTemplate = 'blog-p%d.html', $urlTemplate = '/blog-p%d.html')
    {
        $this->prepareEntries();

        // Get a paginator
        $paginator = $this->getPaginator($this->entries);

        // Loop through pages
        $pageCount = count($paginator);
        for ($i = 1; $i <= $pageCount; $i++) {
            $paginator->setCurrentPageNumber($i);

            $filename = sprintf($filenameTemplate, $i);
            $url      = sprintf($urlTemplate, $i);

            // Generate this page
            $model = array(
                'title'         => 'Blog Entries',
                'entries'       => $paginator,
                'paginator_url' => $url,
            );

            $this->prepareResponseStrategy($filename);
            $this->view->render($model);
        }
    }

    public function compilePaginatedEntriesByYear($template, $filenameTemplate = 'year/%s-p%d.html', $urlTemplate = '/blog/year/%s-p%d.html')
    {
        $this->prepareEntries();
        foreach ($this->byYear as $year => $list) {
            // Get a paginator for this day
            $paginator = $this->getPaginator($list);

            // Loop through pages
            $pageCount = count($paginator);
            for ($i = 1; $i <= $pageCount; $i++) {
                $paginator->setCurrentPageNumber($i);

                $filename = sprintf($filenameTemplate, $year, $i);
                $url      = sprintf($urlTemplate, $year, $i);

                // Generate this page
                $model = array(
                    'title'         => 'Blog Entries for ' . $year,
                    'entries'       => $paginator,
                    'paginator_url' => $url,
                );

                $this->prepareResponseStrategy($filename);
                $this->view->render($model);
            }
        }
    }

    public function compilePaginatedEntriesByMonth($template, $filenameTemplate = 'month/%s-p%d.html', $urlTemplate = '/blog/month/%s-p%d.html')
    {
        $this->prepareEntries();
        foreach ($this->byMonth as $month => $list) {
            // Get a paginator for this day
            $paginator = $this->getPaginator($list);

            // Loop through pages
            $pageCount = count($paginator);
            for ($i = 1; $i <= $pageCount; $i++) {
                $paginator->setCurrentPageNumber($i);

                list($year, $monthDigit) = explode($month, 2);

                $filename = sprintf($filenameTemplate, $month, $i);
                $url      = sprintf($urlTemplate, $month, $i);

                // Generate this page
                $model = array(
                    'title'         => 'Blog Entries for ' . date('F', strtotime($year . '-' . $month . '-01')) . ' ' . $year,
                    'entries'       => $paginator,
                    'paginator_url' => $url,
                );

                $this->prepareResponseStrategy($filename);
                $this->view->render($model);
            }
        }
    }

    public function compilePaginatedEntriesByDate($template, $filenameTemplate = 'day/%s-p%d.html', $urlTemplate = '/blog/day/%s-p%d.html')
    {
        $this->prepareEntries();

        foreach ($this->byDay as $day => $list) {
            // Get a paginator for this day
            $paginator = $this->getPaginator($list);

            // Loop through pages
            $pageCount = count($paginator);
            for ($i = 1; $i <= $pageCount; $i++) {
                $paginator->setCurrentPageNumber($i);

                list($year, $month, $date) = explode($day, 3);

                $filename = sprintf($filenameTemplate, $day, $i);
                $url      = sprintf($urlTemplate, $day, $i);

                // Generate this page
                $model = array(
                    'title'         => 'Blog Entries for ' . $date . ' ' . date('F', strtotime($year . '-' . $month . '-' . $date)) . ' ' . $year,
                    'entries'       => $paginator,
                    'paginator_url' => $url,
                );

                $this->prepareResponseStrategy($filename);
                $this->view->render($model);
            }
        }
    }

    public function compilePaginatedEntriesByTag($template, $filenameTemplate = 'tag/%s-p%d.html', $urlTemplate = '/blog/tag/%s-p%d.html')
    {
        $this->prepareEntries();

        foreach ($this->byTag as $tag => $list) {
            // Get a paginator for this tag
            $paginator = $this->getPaginator($list);

            // Loop through pages
            $pageCount = count($paginator);
            for ($i = 1; $i <= $pageCount; $i++) {
                $paginator->setCurrentPageNumber($i);

                $filename = sprintf($filenameTemplate, $tag, $i);
                $url      = sprintf($urlTemplate, $tag, $i);

                // Generate this page
                $model = array(
                    'title'         => 'Tag: ' . $tag,
                    'tag'           => $tag,
                    'entries'       => $paginator,
                    'paginator_url' => $url,
                );

                $this->prepareResponseStrategy($filename);
                $this->view->render($model);
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
        if ($this->tagCloud) {
            return $this->tagCloud;
        }

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

        $this->tagCloud = new TagCloud($options);
        return $this->tagCloud;
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
            $month = $date->format('Y/m');
            $day   = $date->format('Y/m/d');

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
     * @todo   get count per page and page range from options
     * @param  Iterator $it 
     * @return Paginator
     */
    protected function getPaginator(Iterator $it)
    {
        $paginator = new Paginator(new IteratorPaginator($it));
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(10);
        $paginator->setPageRange(10);
        return $paginator;
    }

    /**
     * Prepare the response strategy
     *
     * Clears out all response listeners, and injects a new callback that 
     * imports the provided filename, and writes the rendering results to
     * that file.
     * 
     * @param  string $filename 
     * @return void
     */
    protected function prepareResponseStrategy($filename)
    {
        if ($this->responseStrategyPrepared) {
            $this->filename->file = $filename;
            return;
        }
        $this->filename = new stdClass;
        $this->filename->file = $filename;
        $filename = $this->filename;

        $this->view->addResponseStrategy(function ($e) use ($filename) {
            $result = $e->getResult();
            file_put_contents($filename->file, $result);
        });
        $this->responseStrategyPrepared = true;
    }
}
