<?php
namespace Blog;

use DateTime,
    DateTimezone,
    DomainException,
    InvalidArgumentException,
    Iterator,
    stdClass,
    Traversable,
    Zend\Feed\Writer\Feed as FeedWriter,
    Zend\Paginator\Paginator,
    Zend\Paginator\Adapter\ArrayAdapter as ArrayPaginator,
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
        $this->prepareEntries();

        foreach ($this->entries as $entry) {
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
        $paginator = $this->getPaginator($this->pagedEntries);

        // Loop through pages
        $pageCount = count($paginator);
        for ($i = 1; $i <= $pageCount; $i++) {
            $paginator->setCurrentPageNumber($i);

            $filename = sprintf($filenameTemplate, $i);

            // Generate this page
            $model = new ViewModel(array(
                'title'         => 'Blog Entries',
                'entries'       => $paginator,
                'paginator_url' => $urlTemplate,
            ));
            $model->setTemplate($template);

            $this->prepareResponseStrategy($filename);
            $this->view->render($model);
            
            // This hack ensures that the paginator is reset for each page
            if ($i <= $pageCount) {
                $paginator = $this->getPaginator($this->entries);
            }
        }
    }

    public function compileRecentFeed($type, $filename = 'blog.xml', $blogLink = '/blog', $feedLink = '/blog.xml', $linkTemplate = '/blog/%s.html', $title = '')
    {
        $type = strtolower($type);
        if (!in_array($type, array('atom', 'rss'))) {
            throw new InvalidArgumentException('Feed type must be "atom" or "rss"');
        }

        $this->prepareEntries();

        // Get a paginator
        $paginator = $this->getPaginator($this->pagedEntries);
        $paginator->setCurrentPageNumber(1);

        $feed = new FeedWriter();
        $feed->setTitle($title);
        $feed->setLink($blogLink);
        $feed->setFeedLink($feedLink, $type);

        // Make this configurable?
        if ('rss' == $type) {
            $feed->setDescription($title);
        }

        $latest = false;
        foreach ($paginator as $post) {
            if (!$latest) {
                $latest = $post;
            }
            $entry = $feed->createEntry();
            $entry->setTitle($post->getTitle());
            $entry->setLink(sprintf($linkTemplate, $post->getId()));

            /**
             * @todo inject this info!
             */
            $entry->addAuthor(array(
                'name'  => "Matthew Weier O'Phinney",
                'email' => 'matthew@weierophinney.net',
                'uri'   => $blogLink,
            ));
            $entry->setDateModified($post->getUpdated());
            $entry->setDateCreated($post->getCreated());
            $entry->setContent($post->getBody());

            $feed->addEntry($entry);
        }

        // Set feed date
        $feed->setDateModified($latest->getUpdated());

        // Write feed to file
        file_put_contents($filename, $feed->export($type));
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

                // Generate this page
                $model = new ViewModel(array(
                    'title'         => 'Blog Entries for ' . $year,
                    'entries'       => $paginator,
                    'paginator_url' => $urlTemplate,
                    'substitution'  => $year,
                ));
                $model->setTemplate($template);

                $this->prepareResponseStrategy($filename);
                $this->view->render($model);
                
                // This hack ensures that the paginator is reset for each page
                if ($i <= $pageCount) {
                    $paginator = $this->getPaginator($list);
                }
            }
        }
    }

    public function compilePaginatedEntriesByMonth($template, $filenameTemplate = 'month/%s-p%d.html', $urlTemplate = '/blog/month/%s-p%d.html')
    {
        $this->prepareEntries();
        foreach ($this->byMonth as $month => $list) {
            // Get a paginator for this day
            $paginator = $this->getPaginator($list);

            // Get the year and month digits
            list($year, $monthDigit) = explode('/', $month, 2);

            // Loop through pages
            $pageCount = count($paginator);
            for ($i = 1; $i <= $pageCount; $i++) {
                $paginator->setCurrentPageNumber($i);

                $filename = sprintf($filenameTemplate, $month, $i);

                // Generate this page
                $model = new ViewModel(array(
                    'title'         => 'Blog Entries for ' . date('F', strtotime($year . '-' . $monthDigit . '-01')) . ' ' . $year,
                    'entries'       => $paginator,
                    'paginator_url' => $urlTemplate,
                    'substitution'  => $month,
                ));
                $model->setTemplate($template);

                $this->prepareResponseStrategy($filename);
                $this->view->render($model);
                
                // This hack ensures that the paginator is reset for each page
                if ($i <= $pageCount) {
                    $paginator = $this->getPaginator($list);
                }
            }
        }
    }

    public function compilePaginatedEntriesByDate($template, $filenameTemplate = 'day/%s-p%d.html', $urlTemplate = '/blog/day/%s-p%d.html')
    {
        $this->prepareEntries();

        foreach ($this->byDay as $day => $list) {
            // Get a paginator for this day
            $paginator = $this->getPaginator($list);
            
            list($year, $month, $date) = explode('/', $day, 3);

            // Loop through pages
            $pageCount = count($paginator);
            for ($i = 1; $i <= $pageCount; $i++) {
                $paginator->setCurrentPageNumber($i);

                $filename = sprintf($filenameTemplate, $day, $i);

                // Generate this page
                $model = new ViewModel(array(
                    'title'         => 'Blog Entries for ' . $date . ' ' . date('F', strtotime($year . '-' . $month . '-' . $date)) . ' ' . $year,
                    'entries'       => $paginator,
                    'paginator_url' => $urlTemplate,
                    'substitution'  => $day,
                ));
                $model->setTemplate($template);

                $this->prepareResponseStrategy($filename);
                $this->view->render($model);
                
                // This hack ensures that the paginator is reset for each page
                if ($i <= $pageCount) {
                    $paginator = $this->getPaginator($list);
                }
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

                // Generate this page
                $model = new ViewModel(array(
                    'title'         => 'Tag: ' . $tag,
                    'tag'           => $tag,
                    'entries'       => $paginator,
                    'paginator_url' => $urlTemplate,
                    'substitution'  => $tag,
                ));
                $model->setTemplate($template);

                $this->prepareResponseStrategy($filename);
                $this->view->render($model);
                
                // This hack ensures that the paginator is reset for each page
                if ($i <= $pageCount) {
                    $paginator = $this->getPaginator($list);
                }
            }
        }
    }

    public function compileTagFeeds($type, $filenameTemplate = 'blog/tag/%s.xml', $blogLinkTemplate = '/blog/%s', $feedLinkTemplate = '/blog/tag/%s.xml', $linkTemplate = '/blog/%s.html', $titleTemplate = 'Tag: %s')
    {
        $type = strtolower($type);
        if (!in_array($type, array('atom', 'rss'))) {
            throw new InvalidArgumentException('Feed type must be "atom" or "rss"');
        }

        $this->prepareEntries();

        foreach ($this->byTag as $tag => $list) {
            // Get a paginator
            $paginator = $this->getPaginator($list);
            $paginator->setCurrentPageNumber(1);

            $title    = sprintf($titleTemplate, $tag);
            $filename = sprintf($filenameTemplate, $tag);
            $blogLink = sprintf($blogLinkTemplate, $tag);
            $feedLink = sprintf($feedLinkTemplate, $tag);
            
            $feed = new FeedWriter();
            $feed->setTitle($title);
            $feed->setLink($blogLink);
            $feed->setFeedLink($feedLink, $type);

            // Make this configurable?
            if ('rss' == $type) {
                $feed->setDescription($title);
            }

            $latest = false;
            foreach ($paginator as $post) {
                if (!$latest) {
                    $latest = $post;
                }
                $entry = $feed->createEntry();
                $entry->setTitle($post->getTitle());
                $entry->setLink(sprintf($linkTemplate, $post->getId()));

                /**
                * @todo inject this info!
                */
                $entry->addAuthor(array(
                    'name'  => "Matthew Weier O'Phinney",
                    'email' => 'matthew@weierophinney.net',
                    'uri'   => str_replace('%s', '', $blogLinkTemplate),
                ));
                $entry->setDateModified($post->getUpdated());
                $entry->setDateCreated($post->getCreated());
                $entry->setContent($post->getBody());

                $feed->addEntry($entry);
            }

            // Set feed date
            $feed->setDateModified($latest->getUpdated());

            // Write feed to file
            file_put_contents($filename, $feed->export($type));
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
            $tags[$tag] = array(
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
            return;
        }

        $this->entries      = new Compiler\SortedEntries();
        $this->pagedEntries = new Compiler\SortedEntries();
        $this->byYear       = array();
        $this->byMonth      = array();
        $this->byDay        = array();
        $this->byTag        = array();
        $this->byAuthor     = array();
        foreach ($this->files as $file) {
            $entry = include $file->getRealPath();
            if (!$entry instanceof EntryEntity) {
                continue;
            }

            if ($entry->isDraft()) {
                continue;
            }

            // First, set in entries
            $timestamp = $entry->getCreated();
            $this->entries->insert($entry, $timestamp);

            // Second, test if it's public; if not, continue to the next
            if (!$entry->isPublic()) {
                continue;
            }

            // Third, add to a special "paginated entries" list
            $this->pagedEntries->insert($entry, $timestamp);

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
            $this->byYear[$year]->insert($entry, $timestamp);

            if (!isset($this->byMonth[$month])) {
                $this->byMonth[$month] = new Compiler\SortedEntries();
            }
            $this->byMonth[$month]->insert($entry, $timestamp);

            if (!isset($this->byDay[$day])) {
                $this->byDay[$day] = new Compiler\SortedEntries();
            }
            $this->byDay[$day]->insert($entry, $timestamp);

            // Next, set in appropriate tag lists
            foreach ($entry->getTags() as $tag) {
                if (!isset($this->byTag[$tag])) {
                    $this->byTag[$tag] = new Compiler\SortedEntries();
                }
                $this->byTag[$tag]->insert($entry, $timestamp);
            }

            // Finally, by author
            $author = $entry->getAuthor();
            if (!isset($this->byAuthor[$author])) {
                $this->byAuthor[$author] = new Compiler\SortedEntries();
            }
            $this->byAuthor[$author]->insert($entry, $timestamp);
        }

        // Cast to array to ensure we can loop through it multiple
        // times; fixes the issue that a Heap removes entries during iteration
        $this->entries      = iterator_to_array($this->entries);
        $this->pagedEntries = iterator_to_array($this->pagedEntries);

        foreach (array('byYear', 'byMonth', 'byDay', 'byTag', 'byAuthor') as $prop) {
            foreach ($this->$prop as $index => $heap) {
                // have to do this due to dynamic resolution order in PHP
                $local =& $this->$prop;
                $local[$index] = iterator_to_array($heap);
            }
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
     * @param  Iterator|array $it 
     * @return Paginator
     * @throws DomainException
     */
    protected function getPaginator(array $list)
    {
        $paginator = new Paginator(new ArrayPaginator($list));
        $paginator->setItemCountPerPage(10);
        $paginator->setPageRange(10);
        return $paginator;
    }

    /**
     * Prepare the response strategy
     *
     * Injects a new callback that imports the provided filename, and writes 
     * the rendering results to that file.
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
            $dir    = dirname($filename->file);
            if (!file_exists($dir) || !is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            file_put_contents($filename->file, $result);
        });
        $this->responseStrategyPrepared = true;
    }
}
