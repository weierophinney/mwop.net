<?php
namespace PhlyBlog;

use Zend\Stdlib\Options,
    Zend\Uri\UriFactory;

class CompilerOptions extends Options
{
    protected $entryTemplate;

    public function setEntryTemplate($entryTemplate)
    {
        $this->entryTemplate = (string) $entryTemplate;
        return $this;
    }

    public function getEntryTemplate()
    {
        return $this->entryTemplate;
    }

    protected $entryFilenameTemplate = '%s.html';

    public function setEntryFilenameTemplate($entryFilenameTemplate)
    {
        $this->entryFilenameTemplate = (string) $entryFilenameTemplate;
        return $this;
    }

    public function getEntryFilenameTemplate()
    {
        return $this->entryFilenameTemplate;
    }


    /* Used everywhere */
    protected $entryLinkTemplate = '/blog/%s.html';

    public function setEntryLinkTemplate($entryLinkTemplate)
    {
        $this->entryLinkTemplate = (string) $entryLinkTemplate;
        return $this;
    }

    public function getEntryLinkTemplate()
    {
        return $this->entryLinkTemplate;
    }

    /* Used for feeds */
    protected $feedHostname = 'http://localhost';

    public function setFeedHostname($feedHostname)
    {
        $this->feedHostname = (string) $feedHostname;
        return $this;
    }

    public function getFeedHostname()
    {
        return $this->feedHostname;
    }


    protected $entriesTemplate;

    public function setEntriesTemplate($entriesTemplate)
    {
        $this->entriesTemplate = (string) $entriesTemplate;
        return $this;
    }

    public function getEntriesTemplate()
    {
        return $this->entriesTemplate;
    }

    protected $entriesFilenameTemplate = 'blog-p%d.html';

    public function setEntriesFilenameTemplate($entriesFilenameTemplate)
    {
        $this->entriesFilenameTemplate = (string) $entriesFilenameTemplate;
        return $this;
    }

    public function getEntriesFilenameTemplate()
    {
        return $this->entriesFilenameTemplate;
    }

    protected $entriesUrlTemplate = '/blog-p%d.html';

    public function setEntriesUrlTemplate($entriesUrlTemplate)
    {
        $this->entriesUrlTemplate = (string) $entriesUrlTemplate;
        return $this;
    }

    public function getEntriesUrlTemplate()
    {
        return $this->entriesUrlTemplate;
    }

    protected $entriesTitle = 'Blog Entries';

    public function setEntriesTitle($entriesTitle)
    {
        $this->entriesTitle = (string) $entriesTitle;
        return $this;
    }

    public function getEntriesTitle()
    {
        return $this->entriesTitle;
    }

    protected $feedFilename = 'blog-%s.xml';

    public function setFeedFilename($feedFilename)
    {
        $this->feedFilename = (string) $feedFilename;
        return $this;
    }

    public function getFeedFilename()
    {
        return $this->feedFilename;
    }

    protected $feedBlogLink = '/blog.html';

    public function setFeedBlogLink($feedBlogLink)
    {
        $this->feedBlogLink = (string) $feedBlogLink;
        return $this;
    }

    public function getFeedBlogLink()
    {
        return $this->feedBlogLink;
    }

    protected $feedFeedLink = '/blog-%s.xml';

    public function setFeedFeedLink($feedFeedLink)
    {
        $this->feedFeedLink = (string) $feedFeedLink;
        return $this;
    }

    public function getFeedFeedLink()
    {
        return $this->feedFeedLink;
    }

    protected $feedTitle    = 'Blog';

    public function setFeedTitle($feedTitle)
    {
        $this->feedTitle = (string) $feedTitle;
        return $this;
    }

    public function getFeedTitle()
    {
        return $this->feedTitle;
    }


    protected $byYearTemplate;

    public function setByYearTemplate($byYearTemplate)
    {
        $this->byYearTemplate = (string) $byYearTemplate;
        return $this;
    }

    public function getByYearTemplate()
    {
        $template = $this->byYearTemplate;
        if (empty($template)) {
            $template = $this->getEntriesTemplate();
        }
        return $template;
    }

    protected $byYearFilenameTemplate = 'year/%s-p%d.html';

    public function setByYearFilenameTemplate($byYearFilenameTemplate)
    {
        $this->byYearFilenameTemplate = (string) $byYearFilenameTemplate;
        return $this;
    }

    public function getByYearFilenameTemplate()
    {
        return $this->byYearFilenameTemplate;
    }

    protected $byYearUrlTemplate = '/blog/year/%s-p%d.html';

    public function setByYearUrlTemplate($byYearUrlTemplate)
    {
        $this->byYearUrlTemplate = (string) $byYearUrlTemplate;
        return $this;
    }

    public function getByYearUrlTemplate()
    {
        return $this->byYearUrlTemplate;
    }

    protected $byYearTitle = 'Blog Entries for %d';

    public function setByYearTitle($byYearTitle)
    {
        $this->byYearTitle = (string) $byYearTitle;
        return $this;
    }

    public function getByYearTitle()
    {
        return $this->byYearTitle;
    }

    protected $byMonthTemplate;

    public function setByMonthTemplate($byMonthTemplate)
    {
        $this->byMonthTemplate = (string) $byMonthTemplate;
        return $this;
    }

    public function getByMonthTemplate()
    {
        $template = $this->byMonthTemplate;
        if (empty($template)) {
            $template = $this->getEntriesTemplate();
        }
        return $template;
    }

    protected $byMonthFilenameTemplate = 'month/%s-p%d.html';

    public function setByMonthFilenameTemplate($byMonthFilenameTemplate)
    {
        $this->byMonthFilenameTemplate = (string) $byMonthFilenameTemplate;
        return $this;
    }

    public function getByMonthFilenameTemplate()
    {
        return $this->byMonthFilenameTemplate;
    }

    protected $byMonthUrlTemplate = '/blog/month/%s-p%d.html';

    public function setByMonthUrlTemplate($byMonthUrlTemplate)
    {
        $this->byMonthUrlTemplate = (string) $byMonthUrlTemplate;
        return $this;
    }

    public function getByMonthUrlTemplate()
    {
        return $this->byMonthUrlTemplate;
    }

    protected $byMonthTitle = 'Blog Entries for %s';

    public function setByMonthTitle($byMonthTitle)
    {
        $this->byMonthTitle = (string) $byMonthTitle;
        return $this;
    }

    public function getByMonthTitle()
    {
        return $this->byMonthTitle;
    }


    protected $byDayTemplate;

    public function setByDayTemplate($byDayTemplate)
    {
        $this->byDayTemplate = (string) $byDayTemplate;
        return $this;
    }

    public function getByDayTemplate()
    {
        $template = $this->byDayTemplate;
        if (empty($template)) {
            $template = $this->getEntriesTemplate();
        }
        return $template;
    }

    protected $byDayFilenameTemplate = 'day/%s-p%d.html';

    public function setByDayFilenameTemplate($byDayFilenameTemplate)
    {
        $this->byDayFilenameTemplate = (string) $byDayFilenameTemplate;
        return $this;
    }

    public function getByDayFilenameTemplate()
    {
        return $this->byDayFilenameTemplate;
    }

    protected $byDayUrlTemplate = '/blog/day/%s-p%d.html';

    public function setByDayUrlTemplate($byDayUrlTemplate)
    {
        $this->byDayUrlTemplate = (string) $byDayUrlTemplate;
        return $this;
    }

    public function getByDayUrlTemplate()
    {
        return $this->byDayUrlTemplate;
    }

    protected $byDayTitle = 'Blog Entries for %s';

    public function setByDayTitle($byDayTitle)
    {
        $this->byDayTitle = (string) $byDayTitle;
        return $this;
    }

    public function getByDayTitle()
    {
        return $this->byDayTitle;
    }


    protected $byTagTemplate;

    public function setByTagTemplate($byTagTemplate)
    {
        $this->byTagTemplate = (string) $byTagTemplate;
        return $this;
    }

    public function getByTagTemplate()
    {
        $template = $this->byTagTemplate;
        if (empty($template)) {
            $template = $this->getEntriesTemplate();
        }
        return $template;
    }

    protected $byTagFilenameTemplate = 'tag/%s-p%d.html';

    public function setByTagFilenameTemplate($byTagFilenameTemplate)
    {
        $this->byTagFilenameTemplate = (string) $byTagFilenameTemplate;
        return $this;
    }

    public function getByTagFilenameTemplate()
    {
        return $this->byTagFilenameTemplate;
    }

    protected $byTagUrlTemplate = '/blog/tag/%s-p%d.html';

    public function setByTagUrlTemplate($byTagUrlTemplate)
    {
        $this->byTagUrlTemplate = (string) $byTagUrlTemplate;
        return $this;
    }

    public function getByTagUrlTemplate()
    {
        return $this->byTagUrlTemplate;
    }

    protected $byTagTitle = 'Tag: %s';

    public function setByTagTitle($byTagTitle)
    {
        $this->byTagTitle = (string) $byTagTitle;
        return $this;
    }

    public function getByTagTitle()
    {
        return $this->byTagTitle;
    }


    protected $tagFeedFilenameTemplate = 'blog/tag/%s-%s.xml';

    public function setTagFeedFilenameTemplate($tagFeedFilenameTemplate)
    {
        $this->tagFeedFilenameTemplate = (string) $tagFeedFilenameTemplate;
        return $this;
    }

    public function getTagFeedFilenameTemplate()
    {
        return $this->tagFeedFilenameTemplate;
    }

    protected $tagFeedBlogLinkTemplate = '/blog/tag/%s.html';

    public function setTagFeedBlogLinkTemplate($tagFeedBlogLinkTemplate)
    {
        $this->tagFeedBlogLinkTemplate = (string) $tagFeedBlogLinkTemplate;
        return $this;
    }

    public function getTagFeedBlogLinkTemplate()
    {
        return $this->tagFeedBlogLinkTemplate;
    }

    protected $tagFeedFeedLinkTemplate = '/blog/tag/%s-%s.xml';

    public function setTagFeedFeedLinkTemplate($tagFeedFeedLinkTemplate)
    {
        $this->tagFeedFeedLinkTemplate = (string) $tagFeedFeedLinkTemplate;
        return $this;
    }

    public function getTagFeedFeedLinkTemplate()
    {
        return $this->tagFeedFeedLinkTemplate;
    }

    protected $tagFeedTitleTemplate = 'Tag: %s';

    public function setTagFeedTitleTemplate($tagFeedTitleTemplate)
    {
        $this->tagFeedTitleTemplate = (string) $tagFeedTitleTemplate;
        return $this;
    }

    public function getTagFeedTitleTemplate()
    {
        return $this->tagFeedTitleTemplate;
    }


    protected $tagCloudUrlTemplate = '/blog/tag/%s.html';

    public function setTagCloudUrlTemplate($tagCloudUrlTemplate)
    {
        $this->tagCloudUrlTemplate = (string) $tagCloudUrlTemplate;
        return $this;
    }

    public function getTagCloudUrlTemplate()
    {
        return $this->tagCloudUrlTemplate;
    }

    protected $tagCloudOptions = array();

    public function setTagCloudOptions(array $tagCloudOptions)
    {
        $this->tagCloudOptions = $tagCloudOptions;
        return $this;
    }

    public function getTagCloudOptions()
    {
        return $this->tagCloudOptions;
    }


    protected $paginatorItemCountPerPage = 10;

    public function setPaginatorItemCountPerPage($paginatorItemCountPerPage)
    {
        $paginatorItemCountPerPage = (int) $paginatorItemCountPerPage;
        if ($paginatorItemCountPerPage < 1) {
            throw new \InvalidArgumentException('Paginator item count per page must be at least 1');
        }
        $this->paginatorItemCountPerPage = (int) $paginatorItemCountPerPage;
        return $this;
    }

    public function getPaginatorItemCountPerPage()
    {
        return $this->paginatorItemCountPerPage;
    }

    protected $paginatorPageRange = 10;

    public function setPaginatorPageRange($paginatorPageRange)
    {
        $paginatorPageRange = (int) $paginatorPageRange;
        if ($paginatorPageRange < 2) {
            throw new \InvalidArgumentException('Paginator page range must be >= 2');
        }
        $this->paginatorPageRange = (int) $paginatorPageRange;
        return $this;
    }

    public function getPaginatorPageRange()
    {
        return $this->paginatorPageRange;
    }


    protected $feedAuthorName = '';

    public function setFeedAuthorName($feedAuthorName)
    {
        $this->feedAuthorName = (string) $feedAuthorName;
        return $this;
    }

    public function getFeedAuthorName()
    {
        return $this->feedAuthorName;
    }

    protected $feedAuthorEmail = '';

    public function setFeedAuthorEmail($feedAuthorEmail)
    {
        $this->feedAuthorEmail = (string) $feedAuthorEmail;
        return $this;
    }

    public function getFeedAuthorEmail()
    {
        return $this->feedAuthorEmail;
    }

    protected $feedAuthorUri = null;

    public function setFeedAuthorUri($feedAuthorUri)
    {
        $uri = UriFactory::factory($feedAuthorUri);
        if (!$uri->isValid()) {
            throw new \InvalidArgumentException('Author URI for feed is invalid');
        }
        $this->feedAuthorUri = $feedAuthorUri;
        return $this;
    }

    public function getFeedAuthorUri()
    {
        return $this->feedAuthorUri;
    }
}
