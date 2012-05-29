<?php
return array(
    'blog' => array(
        'options' => array(
            'author_feed_filename_template' => 'public/blog/author/%s-%s.xml',
            'author_feed_title_template'    => 'Author: %s :: phly, boy, phly',
            'by_author_filename_template'   => 'public/blog/author/%s-p%d.html',
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
        'posts_path'     => 'content/posts/',
        'view_callback'  => array('Application\Module', 'prepareCompilerView'),
        'cloud_callback' => array('Application\Module', 'handleTagCloud'),
    ),
    'view_manager' => array(
        'template_map' => array(
            'blog/assets'       => 'module/Application/view/blog/assets.phtml',
            'blog/blogroll'     => 'module/Application/view/blog/blogroll.phtml',
            'blog/entry-short'  => 'module/Application/view/blog/entry-short.phtml',
            'blog/entry'        => 'module/Application/view/blog/entry.phtml',
            'blog/list'         => 'module/Application/view/blog/list.phtml',
            'blog/paginator'    => 'module/Application/view/blog/paginator.phtml',
            'blog/social-media' => 'module/Application/view/blog/social-media.phtml',
            'blog/tags'         => 'module/Application/view/blog/tags.phtml',
        ),
        'template_path_stack' => array(
            'blog' => 'module/Application/view',
        ),
    ),
);
