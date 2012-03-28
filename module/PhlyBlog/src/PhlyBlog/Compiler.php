<?php
namespace PhlyBlog;

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
    protected $options;
    protected $responseStrategyPrepared = false;
    protected $tagCloud;
    protected $view;
    protected $writer;

    public function __construct(Compiler\PhpFileFilter $files, View $view, Compiler\WriterInterface $writer, CompilerOptions $options = null)
    {
        $this->files  = $files;
        $this->view   = $view;
        $this->writer = $writer;
        if (null === $options) {
            $options = new CompilerOptions;
        }
        $this->options = $options;
    }

    public function compileEntryViewScripts($template = null)
    {
        if (null === $template) {
            $template = $this->options->getEntryTemplate();
            if (empty($template)) {
                throw new \DomainException('No template provided for individual entries');
            }
        }
        $filenameTemplate = $this->options->getEntryFilenameTemplate();

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

    public function compilePaginatedEntries($template = null)
    {
        if (null === $template) {
            $template = $this->options->getEntriesTemplate();
            if (empty($template)) {
                throw new \DomainException('No template provided for listing entries');
            }
        }
        $filenameTemplate = $this->options->getEntriesFilenameTemplate();
        $urlTemplate      = $this->options->getEntriesUrlTemplate();
        $title            = $this->options->getEntriesTitle();

        $this->prepareEntries();

        $this->iterateAndRenderList(
            $this->pagedEntries,
            $filenameTemplate,
            array(),
            $title,
            $urlTemplate,
            false,
            $template
        );
    }

    public function compileRecentFeed($type, $title = '')
    {
        $type = strtolower($type);
        if (!in_array($type, array('atom', 'rss'))) {
            throw new InvalidArgumentException('Feed type must be "atom" or "rss"');
        }

        $filename     = $this->options->getFeedFilename();
        $blogLink     = $this->options->getFeedBlogLink();
        $feedLink     = $this->options->getFeedFeedLink();
        $title        = $this->options->getFeedTitle();

        $this->prepareEntries();

        $this->iterateAndGenerateFeed(
            $type,
            $this->pagedEntries,
            $title,
            $blogLink,
            $feedLink,
            $filename
        );
    }

    public function compilePaginatedEntriesByYear($template = null)
    {
        if (null === $template) {
            $template = $this->options->getByYearTemplate();
            if (empty($template)) {
                throw new \DomainException('No template provided for listing entries by year');
            }
        }

        $filenameTemplate = $this->options->getByYearFilenameTemplate();
        $urlTemplate      = $this->options->getByYearUrlTemplate();
        $titleTemplate    = $this->options->getByYearTitle();

        $this->prepareEntries();
        foreach ($this->byYear as $year => $list) {
            $this->iterateAndRenderList(
                $list,
                $filenameTemplate,
                array($year),
                sprintf($titleTemplate, $year),
                $urlTemplate,
                $year,
                $template
            );
        }
    }

    public function compilePaginatedEntriesByMonth($template = null)
    {
        if (null === $template) {
            $template = $this->options->getByMonthTemplate();
            if (empty($template)) {
                throw new \DomainException('No template provided for listing entries by month');
            }
        }

        $filenameTemplate = $this->options->getByMonthFilenameTemplate();
        $urlTemplate      = $this->options->getByMonthUrlTemplate();
        $titleTemplate    = $this->options->getByMonthTitle();

        $this->prepareEntries();

        foreach ($this->byMonth as $month => $list) {
            // Get the year and month digits
            list($year, $monthDigit) = explode('/', $month, 2);

            $this->iterateAndRenderList(
                $list,
                $filenameTemplate,
                array($month),
                sprintf($titleTemplate, date('F', strtotime($year . '-' . $monthDigit . '-01')) . ' ' . $year),
                $urlTemplate,
                $month,
                $template
            );
        }
    }

    public function compilePaginatedEntriesByDate($template = null)
    {
        if (null === $template) {
            $template = $this->options->getByDayTemplate();
            if (empty($template)) {
                throw new \DomainException('No template provided for listing entries by day');
            }
        }

        $filenameTemplate = $this->options->getByDayFilenameTemplate();
        $urlTemplate      = $this->options->getByDayUrlTemplate();
        $titleTemplate    = $this->options->getByDayTitle();

        $this->prepareEntries();

        foreach ($this->byDay as $day => $list) {
            // Get the year, month, and day digits
            list($year, $month, $date) = explode('/', $day, 3);

            $this->iterateAndRenderList(
                $list,
                $filenameTemplate,
                array($day),
                sprintf($titleTemplate, $date . ' ' . date('F', strtotime($year . '-' . $month . '-' . $date)) . ' ' . $year),
                $urlTemplate,
                $day,
                $template
            );
        }
    }

    public function compilePaginatedEntriesByTag($template = null)
    {
        if (null === $template) {
            $template = $this->options->getByTagTemplate();
            if (empty($template)) {
                throw new \DomainException('No template provided for listing entries by tag');
            }
        }

        $filenameTemplate = $this->options->getByTagFilenameTemplate();
        $urlTemplate      = $this->options->getByTagUrlTemplate();
        $titleTemplate    = $this->options->getByTagTitle();

        $this->prepareEntries();

        foreach ($this->byTag as $tag => $list) {
            $this->iterateAndRenderList(
                $list,
                $filenameTemplate,
                array($tag),
                sprintf($titleTemplate, $tag),
                $urlTemplate,
                $tag,
                $template
            );
        }
    }

    public function compileTagFeeds($type)
    {
        $type = strtolower($type);
        if (!in_array($type, array('atom', 'rss'))) {
            throw new InvalidArgumentException('Feed type must be "atom" or "rss"');
        }

        $filenameTemplate = $this->options->getTagFeedFilenameTemplate();
        $blogLinkTemplate = $this->options->getTagFeedBlogLinkTemplate();
        $feedLinkTemplate = $this->options->getTagFeedFeedLinkTemplate();
        $titleTemplate    = $this->options->getTagFeedTitleTemplate();

        $this->prepareEntries();

        foreach ($this->byTag as $tag => $list) {
            $title    = sprintf($titleTemplate, $tag);
            $filename = sprintf($filenameTemplate, $tag, $type);
            $blogLink = sprintf($blogLinkTemplate, str_replace(' ', '+', $tag));
            $feedLink = sprintf($feedLinkTemplate, str_replace(' ', '+', $tag), $type);

            $this->iterateAndGenerateFeed(
                $type,
                $list,
                $title,
                $blogLink,
                $feedLink,
                $filename
            );
        }
    }


    /**
     * Compile a tag cloud from the entries
     *
     * @todo   Should this write the tag cloud markup to a file?
     * @return TagCloud
     */
    public function compileTagCloud()
    {
        if ($this->tagCloud) {
            return $this->tagCloud;
        }

        $tagUrlTemplate = $this->options->getTagCloudUrlTemplate();
        $cloudOptions   = $this->options->getTagCloudOptions();

        $this->prepareEntries();

        $tags = array();
        foreach ($this->byTag as $tag => $list) {
            $tags[$tag] = array(
                'title'   => $tag,
                'weight'  => count($list),
                'params'  => array(
                    'url' => sprintf($tagUrlTemplate, str_replace(' ', '+', $tag)),
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
     * @param  Iterator|array $it 
     * @return Paginator
     * @throws DomainException
     */
    protected function getPaginator(array $list)
    {
        $paginator = new Paginator(new ArrayPaginator($list));
        $paginator->setItemCountPerPage($this->options->getPaginatorItemCountPerPage());
        $paginator->setPageRange($this->options->getPaginatorPageRange());
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
        $writer   = $this->writer;

        $this->view->addResponseStrategy(function ($e) use ($filename, $writer) {
            $result = $e->getResult();
            $file   = $filename->file;
            $dir    = dirname($file);
            if (!file_exists($dir) || !is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            if (preg_match('/-p1.html$/', $file)) {
                $file = preg_replace('/-p1(\.html)$/', '$1', $file);
            }
            $file = str_replace(' ', '+', $file);
            $writer->write($file, $result);
        });
        $this->responseStrategyPrepared = true;
    }

    protected function iterateAndRenderList(
        $list, 
        $filenameTemplate, 
        array $filenameSubs, 
        $title, 
        $urlTemplate,
        $substitution, 
        $template
    ) {
        // Get a paginator for this day
        $paginator = $this->getPaginator($list);

        // Loop through pages
        $pageCount = count($paginator);
        for ($i = 1; $i <= $pageCount; $i++) {
            $paginator->setCurrentPageNumber($i);

            $substitutions   = $filenameSubs;
            $substitutions[] = $i;
            $filename = vsprintf($filenameTemplate, $substitutions);

            // Generate this page
            $model = new ViewModel(array(
                'title'         => $title,
                'entries'       => $paginator,
                'paginator_url' => $urlTemplate,
                'substitution'  => $substitution,
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

    protected function iterateAndGenerateFeed(
        $type,
        $list,
        $title,
        $blogLink,
        $feedLinkTemplate,
        $filenameTemplate
    ) {
        $blogLink         = $this->options->getFeedHostname() . $blogLink;
        $feedLinkTemplate = $this->options->getFeedHostname() . $feedLinkTemplate;
        $linkTemplate     = $this->options->getFeedHostname() . $this->options->getEntryLinkTemplate();

        // Get a paginator
        $paginator = $this->getPaginator($this->pagedEntries);
        $paginator->setCurrentPageNumber(1);

        $feed = new FeedWriter();
        $feed->setTitle($title);
        $feed->setLink($blogLink);
        $feed->setFeedLink(sprintf($feedLinkTemplate, $type), $type);

        if ('rss' == $type) {
            $feed->setDescription($title);
        }

        $authorUri   = $this->options->getFeedAuthorUri();
        if (empty($authorUri)) {
            $authorUri = $blogLink;
        }
        $author      = array(
            'name'  => $this->options->getFeedAuthorName(),
            'email' => $this->options->getFeedAuthorEmail(),
            'uri'   => $authorUri,
        );

        $latest = false;
        foreach ($paginator as $post) {
            if (!$latest) {
                $latest = $post;
            }
            $entry = $feed->createEntry();
            $entry->setTitle($post->getTitle());
            $entry->setLink(sprintf($linkTemplate, $post->getId()));

            $entry->addAuthor($author);
            $entry->setDateModified($post->getUpdated());
            $entry->setDateCreated($post->getCreated());
            $entry->setContent($post->getBody());

            $feed->addEntry($entry);
        }

        // Set feed date
        $feed->setDateModified($latest->getUpdated());

        // Write feed to file
        $this->writer->write(sprintf($filenameTemplate, $type), $feed->export($type));
    }
}
