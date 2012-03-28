Static Blog
===========

This module is a tool for generating a static blog.

Blog posts are simply PHP files that create and return `Blog\EntryEntity`
objects. You point the compiler at a directory, and it creates a tree of files
representing your blog and its feeds. These can either be consumed by your
application, or they can be plain old HTML markup files that you serve
directly.

Requirements
----

* PHP >= 5.3.3
* Zend Framework 2 >= 2.0.0beta3, specifically: 
  * Zend\View\View, used to render and write generated files
  * Zend\Mvc and Zend\Module, as this implements a module, and the compiler
    script depends on it and an Application instance. As such, it also has
    a dependency on Zend\Loader, Zend\Di, and Zend\EventManager.
  * Zend\Feed\Writer
  * Zend\Tag\Cloud
* PhlyCommon (for Entity and Filter interfaces)

Writing Entries
====

Find a location in your repository for entries, preferably outside your document
root; I recommend either `data/blog/` or `posts/`.

Post files are simply PHP files that return a `Blog\EntryEntity` instance. A
sample is provided in `misc/sample-post.php`. This post can be copied as a
template.

Important things to note:

* Set the created and/or updated timestamps. Alternately, use `DateTime` or
  `date()` to generate a timestamp based on a date/time string.
* Entries marked as "drafts" (i.e., `setDraft(true)`) will not be published.
* Entries marked as private (i.e., `setPublic(false)`) will be published, but
  will not be aggregated in paginated views or feeds. As such, you need to hand
  the URL to somebody in order for them to see it.
* You can set an array of tags. Tags can have whitespace, which will be
  translated to "+" characters.

Usage
=====

A script, `bin/compile.php`, is shipped for your convenience, and it will
generate the following artifacts:

* A file per entry
* Paginated entry files
* Paginated entry files by year
* Paginated entry files by month
* Paginated entry files by day
* Paginated entry files by tag
* Atom and/or RSS feeds for recent entries
* Atom and/or RSS feeds for recent entries by tag
* Optionally, a tag cloud

The script, makes the following assumptions:

* They are being called by another script that:
  * sets up one or more autoloaders, including functionality to autoload the
    code in this library
  * compiles and merges all application configuration
  * bootstraps the application
  * retains the Application instance in the current scope

Basically, a script that does normal bootstrapping, but without calling `run()`
or `send()` on the Application instance.

You will want to setup local configuration; I recommend putting it in
`config/autoload/module.blog.config.global.php`. As a sample:

    ```php
    <?php
    return array(
    'blog' => array(
        'options' => array(
            // The following indicate where to write files. Note that this
            // configuration writes to the "public/" directory, which would
            // create a blog made from static files. For the various
            // paginated views, "%d" is the current page number; "%s" is
            // typically a date string (see below for more information) or tag.
            'by_day_filename_template'   => 'public/blog/day/%s-p%d.html',
            'by_month_filename_template' => 'public/blog/month/%s-p%d.html',
            'by_tag_filename_template'   => 'public/blog/tag/%s-p%d.html',
            'by_year_filename_template'  => 'public/blog/year/%s-p%d.html',
            'entries_filename_template'  => 'public/blog-p%d.html',

            // In this case, the "%s" is the entry ID.
            'entry_filename_template'    => 'public/blog/%s.html',

            // For feeds, the final "%s" is the feed type -- "atom" or "rss". In
            // the case of the tag feed, the initial "%s" is the current tag.
            'feed_filename'              => 'public/blog-%s.xml',
            'tag_feed_filename_template' => 'public/blog/tag/%s-%s.xml',
             
            // This is the link to a blog post
            'entry_link_template'        => '/blog/%s.html',

            // These are the various URL templates for "paginated" views. The
            // "%d" in each is the current page number.
            'entries_url_template'       => '/blog-p%d.html',
            // For the year/month/day paginated views, "%s" is a string
            // representing the date. By default, this will be "YYYY",
            // "YYYY/MM", and "YYYY/MM/DD", respectively.
            'by_year_url_template'       => '/blog/year/%s-p%d.html',
            'by_month_url_template'      => '/blog/month/%s-p%d.html',
            'by_day_url_template'        => '/blog/day/%s-p%d.html',

            // These are the primary templates you will use -- the first is for
            // paginated lists of entries, the second for individual entries.
            // There are of course more templates, but these are the only ones 
            // that will be directly referenced and rendered by the compiler.
            'entries_template'           => 'blog/list',
            'entry_template'             => 'blog/entry',

            'feed_author_email'          => 'me@mwop.net',
            'feed_author_name'           => "Matthew Weier O'Phinney",
            'feed_author_uri'            => 'http://mwop.net',
            'feed_hostname'              => 'http://mwop.net',
            'feed_title'                 => 'Blog Entries :: phly, boy, phly',
            'tag_feed_title_template'    => 'Tag: %s :: phly, boy, phly',

            // If generating a tag cloud, you can specify options for
            // Zend\Tag\Cloud. The following sets up percentage sizing from
            // 80-300%
            'tag_cloud_options'          => array('tagDecorator' => array(
                'decorator' => 'html_tag',
                'options'   => array(
                    'fontSizeUnit' => '%',
                    'minFontSize'  => 80,
                    'maxFontSize'  => 300,
                ),
            )),
        ),
        
        // This is the location where you are keeping your post files (the PHP
        // files returning `Blog\EntryEntity` objects).
        'posts_path'     => 'data/posts/',

        // You can provide your own callback to setup renderer and response
        // strategies. This is useful, for instance, for injecting your 
        // rendered contents into a layout.
        // The callback will receive a View instance, application configuration
        // (as an array), and the application's Locator instance.
        'view_callback'  => array('Application\Module', 'prepareCompilerView'),

        // Tag cloud generation is possible, but you likely need to capture
        // the rendered cloud to inject elsewhere. You can do this with a
        // callback.
        // The callback will receive a Zend\Tag\Cloud instance, the View
        // instance, application configuration // (as an array), and the
        // application's Locator instance.
        'cloud_callback' => array('Application\Module', 'handleTagCloud'),
    ),
    'di' => array('instance' => array(
        // You will likely want to customize the templates provided. Do so by
        // creating your own in your own module, and make sure you alter the
        // resolvers so that they point to the override locations. Below, I'm
        // putting my overrides in my Application module.
        'Zend\View\Resolver\TemplateMapResolver' => array('parameters' => array(
            'map' => array(
                'blog/entry-short'  => 'module/Application/view/blog/entry-short.phtml',
                'blog/entry'        => 'module/Application/view/blog/entry.phtml',
                'blog/list'         => 'module/Application/view/blog/list.phtml',
                'blog/paginator'    => 'module/Application/view/blog/paginator.phtml',
                'blog/tags'         => 'module/Application/view/blog/tags.phtml',
            ),
        )),

        'Zend\View\Resolver\TemplatePathStack' => array('parameters' => array(
            'paths' => array(
                'blog' => 'module/Application/view',
            ),
        )),
    ));

When you run the script, it will generate files in the locations you specify in
your configuration.
