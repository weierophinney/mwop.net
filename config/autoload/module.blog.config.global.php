<?php
$config = array();
$config['blog'] = array(
    'options' => array(
        'by_day_filename_template'   => 'public/blog/day/%s-p%d.html',
        'by_month_filename_template' => 'public/blog/month/%s-p%d.html',
        'by_tag_filename_template'   => 'public/blog/tag/%s-p%d.html',
        'by_year_filename_template'  => 'public/blog/year/%s-p%d.html',
        'entries_filename_template'  => 'public/blog-p%d.html',
        'entries_template'           => 'blog/list',
        'entry_filename_template'    => 'public/blog/%s.html',
        'entry_link_template'        => '/blog/%s.html',
        'entry_template'             => 'blog/entry',
        'feed_author_email'          => 'me@mwop.net',
        'feed_author_name'           => "Matthew Weier O'Phinney",
        'feed_author_uri'            => 'http://mwop.net',
        'feed_filename'              => 'public/blog-%s.xml',
        'feed_hostname'              => 'http://mwop.net',
        'feed_title'                 => 'Blog Entries :: phly, boy, phly',
        'tag_feed_filename_template' => 'public/blog/tag/%s-%s.xml',
        'tag_feed_title_template'    => 'Tag: %s :: phly, boy, phly',
        'tag_cloud_options'          => array('tagDecorator' => array(
            'decorator' => 'html_tag',
            'options'   => array(
                'fontSizeUnit' => '%',
                'minFontSize'  => 80,
                'maxFontSize'  => 300,
            ),
        )),
    ),
    'posts_path'     => 'data/posts/',
    'view_callback'  => array('Application\Module', 'prepareCompilerView'),
    'cloud_callback' => array('Application\Module', 'handleTagCloud'),
);

return $config;
